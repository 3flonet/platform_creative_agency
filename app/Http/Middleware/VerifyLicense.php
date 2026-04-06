<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class VerifyLicense
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🏰 Skip license check if system is not installed yet
        if (!file_exists(storage_path('installed.lock')) || $this->shouldSkipCheck($request)) {
            return $next($request);
        }

        // 🛡️ SECURITY: Checking for missing API configuration
        if (!config('app.license.hub.product_secret') && !env('LICENSE_HUB_PRODUCT_SECRET')) {
            if ($request->is('admin*')) {
                return redirect()->to('/admin/license/activate');
            }
        }

        $status = $this->licenseService->check();
        
        // 1. Check for Major Issues (Status that should block eventually)
        $blockingStatuses = ['revoked', 'suspended', 'tampered', 'invalid_signature_or_response', 'deactivated', 'inactive', 'invalid', 'error', 'expired'];

        if (in_array($status, $blockingStatuses)) {
            $firstDetected = Setting::get('license_issue_detected_at');
            
            // If it's a new issue, record the timestamp
            if (!$firstDetected) {
                $firstDetected = now()->toDateTimeString();
                Setting::updateOrCreate(['key' => 'license_issue_detected_at'], ['value' => $firstDetected]);
            }

            $firstDetectedTime = \Carbon\Carbon::parse($firstDetected);
            $graceUntil = Setting::get('license_grace_until');
            
            // 24 hour grace period for most issues, or use explicit grace_until for 'expired'
            $isGracePeriod = $firstDetectedTime->diffInHours(now()) < 24;

            if ($status === 'expired' && $graceUntil && \Carbon\Carbon::parse($graceUntil)->isFuture()) {
                $isGracePeriod = true;
            }

            // If past grace period -> HARD LOCK
            if (!$isGracePeriod) {
                 // Handle Tamper detection separately with a 403 view
                 if ($status === 'tampered' || $status === 'invalid_signature_or_response') {
                     if ($request->is('admin*')) {
                         return response()->view('errors.license-tampered', [], 403);
                     }
                 }

                 // 🛡️ SUPPORT LIVEWIRE REDIRECT
                 if ($request->header('X-Livewire')) {
                     return response('', 204)->header('X-Livewire-Redirect', url('/admin/license/activate'));
                 }

                 // Standard Redirect
                 if ($request->is('admin*') && !$request->is('admin/license*')) {
                     return redirect()->to('/admin/license/activate');
                 }
            }

            // Still in grace period -> Store in session for UI (Banner)
            // Use max(0, hours) to avoid negative display in UI
            $remainingHours = $graceUntil 
                ? \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($graceUntil), false) 
                : 24 - $firstDetectedTime->diffInHours(now());

            session([
                'license_status' => $status,
                'license_is_grace_period' => true,
                'license_remaining_hours' => max(0, round($remainingHours)),
            ]);
        } else {
            // Clean up detection timestamp if everything is fine
            Setting::where('key', 'license_issue_detected_at')->delete();
            session()->forget(['license_status', 'license_is_grace_period', 'license_grace_until']);
        }

        return $next($request);
    }

    protected function shouldSkipCheck(Request $request): bool
    {
        return $request->is('api*') || 
               $request->is('admin/license*') || 
               $request->is('login') || 
               $request->is('logout') ||
               $request->is('_debugbar*') ||
               $request->is('up');
    }
}
