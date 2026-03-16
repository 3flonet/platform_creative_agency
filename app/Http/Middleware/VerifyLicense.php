<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;
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
        if (!file_exists(storage_path('installed.lock'))) {
            return $next($request);
        }

        // Skip license check for certain conditions

        // 🛡️ SECURITY: Checking for missing API configuration
        if (!config('services.license_hub.product_secret') && !env('LICENSE_HUB_PRODUCT_SECRET')) {
            if ($request->is('admin*')) {
                return redirect()->to('/admin/license/activate'); // Or a dedicated setup page
            }
        }

        $status = $this->licenseService->check();

        if ($status !== 'active') {
             // Handle Tamper detection
             if ($status === 'tampered' || $status === 'invalid_signature') {
                 if ($request->is('admin*')) {
                     return response()->view('errors.license-tampered', [], 403);
                 }
             }

             // If we are in admin, redirect to license activation page
             if ($request->is('admin*') && !$request->is('admin/license*')) {
                 return redirect()->to('/admin/license/activate');
             }
        }

        return $next($request);
    }

    protected function shouldSkipCheck(Request $request): bool
    {
        return $request->is('api*') || 
               $request->is('admin/license*') || 
               $request->is('login') || 
               $request->is('logout') ||
               $request->is('_debugbar*');
    }
}
