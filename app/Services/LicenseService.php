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
        $this->baseUrl = rtrim((string) config('app.license.hub.url', env('LICENSE_HUB_URL', 'https://license.3flo.net')), '/');
        if (empty($this->baseUrl)) {
            $this->baseUrl = 'https://license.3flo.net';
        }
        $this->productSecret = (string) config('app.license.hub.product_secret', env('LICENSE_HUB_PRODUCT_SECRET'));
        
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
            // Trim secret to avoid issues with spaces/quotes
            $this->productSecret = trim($this->productSecret, " \t\n\r\0\x0B\"");
            
            $response = Http::withoutVerifying()->withHeaders([
                'X-Product-Secret' => $this->productSecret,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/licenses/verify", [
                'license_key' => $key,
                'domain' => request()->getHost(),
            ]);

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('📡 Hub Response Fail: ' . $response->status() . ' - ' . $response->body());
            }

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('🔌 Hub Connection Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'License Hub Error: ' . $e->getMessage()];
        }

        if ($response->successful()) {
            $data = $response->json();
            
            // 🛡️ SECURITY: Verify Digital Signature
            if (!is_array($data) || !$this->verifySignature($data)) {
                \Illuminate\Support\Facades\Log::warning('⚠️ Signature Mismatch or Malformed Data. Server Response: ' . json_encode($data));
                return ['success' => false, 'message' => 'Security Gap: Invalid Server Signature or Response'];
            }

            if ($data['status'] === 'active') {
                $isInstalled = file_exists(storage_path('installed.lock'));

                if ($isInstalled) {
                    Setting::updateOrCreate(['key' => 'license_key'], ['value' => $key]);
                    
                    // 🛡️ SECURITY: Environment Binding
                    $statusToken = $this->generateStatusToken('active', $key);
                    Setting::updateOrCreate(['key' => 'license_status_token'], ['value' => $statusToken]);
                    Setting::updateOrCreate(['key' => 'license_status'], ['value' => 'active']);
                    Setting::updateOrCreate(['key' => 'license_expires_at'], ['value' => $data['license']['expires_at'] ?? null]);
                    Setting::updateOrCreate(['key' => 'license_grace_until'], ['value' => $data['license']['grace_period_until'] ?? null]);
                    Setting::updateOrCreate(['key' => 'license_customer_name'], ['value' => $data['customer']['name'] ?? null]);
                    Setting::updateOrCreate(['key' => 'license_customer_email'], ['value' => $data['customer']['email'] ?? null]);
                } else {
                    // During installation, save to session to be persisted later during finalize
                    session([
                        'tmp_license_key' => $key,
                        'tmp_license_status' => 'active',
                        'tmp_license_expires_at' => $data['license']['expires_at'] ?? null,
                        'tmp_license_grace_until' => $data['license']['grace_period_until'] ?? null,
                        'tmp_license_customer_name' => $data['customer']['name'] ?? null,
                        'tmp_license_customer_email' => $data['customer']['email'] ?? null,
                    ]);
                }
                
                Cache::put('license_status', 'active', now()->addDay());
                return ['success' => true, 'message' => $data['message'], 'data' => $data];
            }
        }

        // Catch non-2xx responses
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

                \Illuminate\Support\Facades\Log::info('📶 Hub Ping Check - Status: ' . $response->status() . ' Body: ' . $response->body());

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('🔌 Hub Ping Exception: ' . $e->getMessage());
                return (string) Setting::get('license_status', 'inactive');
            }

            if ($response->successful()) {
                $data = $response->json();

                // 🛡️ SECURITY: Verify Digital Signature
                if (!is_array($data) || !$this->verifySignature($data)) {
                    \Illuminate\Support\Facades\Log::warning('⚠️ Ping Signature Mismatch. Data: ' . json_encode($data));
                    return 'invalid_signature_or_response';
                }

                if (isset($data['status']) && $data['status'] === 'success') {
                    $status = $data['data']['license']['status'] ?? 'active';
                    
                    Setting::updateOrCreate(['key' => 'license_status'], ['value' => $status]);
                    Setting::updateOrCreate(['key' => 'license_status_token'], [
                        'value' => $this->generateStatusToken($status, $this->licenseKey)
                    ]);
                    
                    // Update metadata
                    Setting::updateOrCreate(['key' => 'license_expires_at'], ['value' => $data['data']['license']['expires_at'] ?? null]);
                    Setting::updateOrCreate(['key' => 'license_grace_until'], ['value' => $data['data']['license']['grace_period_until'] ?? null]);
                    Setting::updateOrCreate(['key' => 'license_last_ping_at'], ['value' => now()->toDateTimeString()]);

                    return (string) $status;
                }
            } else {
                $data = $response->json();
                $status = $data['status'] ?? 'invalid';

                \Illuminate\Support\Facades\Log::info('🚫 Hub Explicit Reject - Status: ' . $status);

                if (in_array($status, ['invalid', 'revoked', 'deactivated', 'suspended', 'expired'])) {
                    Setting::updateOrCreate(['key' => 'license_status'], ['value' => $status]);
                    // Clear the signature token for certain states to trigger re-auth if needed
                    if (in_array($status, ['invalid', 'revoked'])) {
                        Setting::where('key', 'license_status_token')->delete();
                    }
                    Cache::forget('license_status');
                    return (string) $status;
                }
            }

            // 📡 OFFLINE: If server is down or returns 500, return last known status from DB
            return (string) Setting::get('license_status', 'inactive');
        });
    }

    /**
     * Manual ping to check status and sync
     */
    public function ping(): array
    {
        Cache::forget('license_status');
        $status = $this->check();
        
        return [
            'success' => in_array($status, ['active', 'expired', 'suspended']),
            'status' => $status,
            'message' => 'License Hub check complete.',
            'license' => [
                'status' => $status,
                'expires_at' => Setting::get('license_expires_at'),
                'grace_until' => Setting::get('license_grace_until'),
                'last_ping_at' => Setting::get('license_last_ping_at'),
            ]
        ];
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
