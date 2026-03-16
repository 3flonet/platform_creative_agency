<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class LicenseService
{
    protected string $baseUrl;
    protected string $productSecret;
    protected ?string $licenseKey;

    public function __construct()
    {
        $this->baseUrl = (string) config('services.license_hub.url', env('LICENSE_HUB_URL', 'https://license.3flo.net/'));
        if (empty($this->baseUrl)) {
            $this->baseUrl = 'https://license.3flo.net/';
        }
        $this->productSecret = (string) config('services.license_hub.product_secret', env('LICENSE_HUB_PRODUCT_SECRET'));
        
        // Resilience during installation: Check if table exists before querying
        $this->licenseKey = env('LICENSE_HUB_KEY');
        
        try {
            if (file_exists(storage_path('installed.lock'))) {
                $this->licenseKey = Setting::get('license_key', $this->licenseKey);
            }
        } catch (\Exception $e) {
            // Silence DB errors during boot/install
        }
    }

    /**
     * Verify and activate a license key
     */
    public function verify(string $key): array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-Product-Secret' => $this->productSecret,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/licenses/verify", [
                'license_key' => $key,
                'domain' => request()->getHost(),
            ]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'License Hub Error: ' . $e->getMessage()];
        }

        if ($response->successful()) {
            $data = $response->json();
            
            // 🛡️ SECURITY: Verify Digital Signature
            if (!is_array($data) || !$this->verifySignature($data)) {
                return ['success' => false, 'message' => 'Security Gap: Invalid Server Signature or Response'];
            }

            if ($data['status'] === 'active') {
                $isInstalled = file_exists(storage_path('installed.lock'));

                if ($isInstalled) {
                    Setting::updateOrCreate(['key' => 'license_key'], ['value' => $key]);
                    
                    // 🛡️ SECURITY: Environment Binding (prevent manual DB editing)
                    $statusToken = $this->generateStatusToken('active', $key);
                    Setting::updateOrCreate(['key' => 'license_status_token'], ['value' => $statusToken]);
                    Setting::updateOrCreate(['key' => 'license_status'], ['value' => 'active']);
                    Setting::updateOrCreate(['key' => 'license_expires_at'], ['value' => $data['license']['expires_at'] ?? null]);
                } else {
                    // During installation, save to session to be persisted later during finalize
                    session([
                        'tmp_license_key' => $key,
                        'tmp_license_status' => 'active',
                        'tmp_license_expires_at' => $data['license']['expires_at'] ?? null
                    ]);
                }
                
                Cache::put('license_status', 'active', now()->addDay());
                return ['success' => true, 'message' => $data['message'], 'data' => $data];
            }
        }

        // Catch non-2xx responses (e.g., 422 "invalid", 401 "unauthorized")
        $errorData = $response->json();
        $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'License verification failed. (HTTP ' . $response->status() . ')';
        return ['success' => false, 'message' => $errorMessage];
    }

    /**
     * Check current license status (cached)
     */
    public function check(): string
    {
        return Cache::remember('license_status', now()->addHours(6), function () {
            if (!$this->licenseKey) {
                return 'inactive';
            }

            // 🛡️ SECURITY: Perform basic integrity check before ping
            if (!$this->validateLocalIntegrity()) {
                return 'tampered';
            }

            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'X-Product-Secret' => $this->productSecret,
                    'Accept' => 'application/json',
                ])->post("{$this->baseUrl}/api/v1/licenses/ping", [
                    'license_key' => $this->licenseKey,
                    'domain' => request()->getHost(),
                ]);
            } catch (\Throwable $e) {
                return Setting::get('license_status', 'inactive');
            }

            if ($response->successful()) {
                $data = $response->json();

                // 🛡️ SECURITY: Verify Digital Signature
                if (!is_array($data) || !$this->verifySignature($data)) {
                    return 'invalid_signature_or_response';
                }

                if (isset($data['status']) && $data['status'] === 'success') {
                    $status = $data['data']['license']['status'] ?? 'active';
                    
                    Setting::updateOrCreate(['key' => 'license_status'], ['value' => $status]);
                    Setting::updateOrCreate(['key' => 'license_status_token'], [
                        'value' => $this->generateStatusToken($status, $this->licenseKey)
                    ]);

                    return $status;
                }
            }

            // If server is down or error, return last known status from DB if integrity is OK
            return Setting::get('license_status', 'inactive');
        });
    }

    /**
     * Verify the HMAC signature from the server
     */
    protected function verifySignature(array $data): bool
    {
        if (!isset($data['signature'])) return false;

        $receivedSignature = $data['signature'];
        unset($data['signature']);
        
        ksort($data);
        $json = json_encode($data);
        $expectedSignature = hash_hmac('sha256', $json, $this->productSecret);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Generate a localized status token bound to APP_KEY
     */
    public function generateStatusToken(string $status, string $key): string
    {
        return hash_hmac('sha256', $status . '|' . $key . '|' . request()->getHost(), config('app.key'));
    }

    /**
     * Check if the local status hasn't been manually manipulated in DB
     */
    protected function validateLocalIntegrity(): bool
    {
        $status = Setting::get('license_status');
        $token = Setting::get('license_status_token');

        if (!$status || !$token) return false;

        $expected = $this->generateStatusToken($status, $this->licenseKey);
        return hash_equals($expected, $token);
    }

    /**
     * Deactivate current license
     */
    public function deactivate(): bool
    {
        if (!$this->licenseKey) return true;

        $response = Http::withHeaders([
            'X-Product-Secret' => $this->productSecret,
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/api/v1/licenses/deactivate", [
            'license_key' => $this->licenseKey,
            'domain' => request()->getHost(),
        ]);

        if ($response->successful()) {
            Setting::where('key', 'license_key')->delete();
            Setting::where('key', 'license_status_token')->delete();
            Setting::updateOrCreate(['key' => 'license_status'], ['value' => 'inactive']);
            Cache::forget('license_status');
            return true;
        }

        return false;
    }
}
