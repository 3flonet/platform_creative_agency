<?php

namespace App\Http\Controllers;

use App\Helpers\EnvHelper;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{
    /**
     * Show the installer wizard
     */
    public function index()
    {
        return view('installer.wizard', [
            'has_product_secret' => !empty(env('LICENSE_HUB_PRODUCT_SECRET')),
        ]);
    }

    /**
     * Step 1: Check Requirements
     */
    public function checkRequirements()
    {
        $requirements = [
            'PHP Version >= 8.2' => PHP_VERSION_ID >= 80200,
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            '.env Writable' => File::isWritable(base_path('.env')),
            'Storage Writable' => File::isWritable(storage_path()),
            'Bootstrap/Cache Writable' => File::isWritable(base_path('bootstrap/cache')),
        ];

        $allMet = !in_array(false, $requirements, true);

        return response()->json([
            'results' => $requirements,
            'success' => $allMet
        ]);
    }

    /**
     * Step 2: Test Database Connection & Save to .env
     */
    public function setupDatabase(Request $request)
    {
        $data = $request->validate([
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        // Attempt to connect temporarily
        try {
            config(['database.connections.setup' => [
                'driver' => 'mysql',
                'host' => $data['host'],
                'database' => $data['database'],
                'username' => $data['username'],
                'password' => $data['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]]);

            DB::connection('setup')->getPdo();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection Failed: ' . $e->getMessage()], 422);
        }

        // Save to .env
        EnvHelper::setMany([
            'DB_HOST' => $data['host'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'] ?? '',
            'APP_URL' => $request->input('app_url', url('/')),
            'APP_ENV' => $request->input('app_env', 'local'),
            'APP_DEBUG' => $request->input('app_debug', 'true'),
            'FILESYSTEM_DISK' => $request->input('filesystem_disk', 'public'),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Step 3: Run Migrations & Seeders
     */
    public function runMigrations(Request $request)
    {
        // 🛡️ SECURITY: Verify signed action token from server (Step 3 bypass prevention)
        // For the sake of this local implementation, we check if license activation happened in session
        if (!session('license_handshake')) {
             return response()->json(['success' => false, 'message' => 'Security Error: Please activate license first.'], 403);
        }

        try {
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Step 4: Verify License (via LicenseService)
     */
    public function verifyLicense(Request $request)
    {
        try {
            $request->validate(['license_key' => 'required']);
            
            // 🧪 If Product Secret is provided in request (because it was missing in .env)
            if ($request->has('product_secret') && !empty($request->product_secret)) {
                EnvHelper::set('LICENSE_HUB_PRODUCT_SECRET', $request->product_secret);
                // Reload config for the current request
                config(['services.license_hub.product_secret' => $request->product_secret]);
            }

            $service = app(LicenseService::class);
            $result = $service->verify($request->license_key);

            if ($result['success']) {
                session(['license_handshake' => true]);
                
                // Persist license key to .env
                EnvHelper::set('LICENSE_HUB_KEY', $request->license_key);
                
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => $result['message']], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Installer Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()], 500);
        }
    }

    /**
     * Step 5: Finalize Installation
     */
    public function finalize()
    {
        try {
            // Generate App Key if empty or placeholder
            if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:unconfigured') {
                Artisan::call('key:generate', ['--force' => true]);
                // Reload config so the new key is used for current request
                Artisan::call('config:clear');
            }

            // Create Symbolic Link for storage
            Artisan::call('storage:link', ['--force' => true]);

            // Create Lock File
            File::put(storage_path('installed.lock'), date('Y-m-d H:i:s'));

            // Sync License data from session after migration is done
            if (session('tmp_license_key')) {
                \App\Models\Setting::updateOrCreate(['key' => 'license_key'], ['value' => session('tmp_license_key')]);
                \App\Models\Setting::updateOrCreate(['key' => 'license_status'], ['value' => 'active']);
                \App\Models\Setting::updateOrCreate(['key' => 'license_expires_at'], ['value' => session('tmp_license_expires_at')]);
                \App\Models\Setting::updateOrCreate(['key' => 'license_grace_until'], ['value' => session('tmp_license_grace_until')]);
                \App\Models\Setting::updateOrCreate(['key' => 'license_customer_name'], ['value' => session('tmp_license_customer_name')]);
                \App\Models\Setting::updateOrCreate(['key' => 'license_customer_email'], ['value' => session('tmp_license_customer_email')]);
                
                // Regenerate status token for security
                $service = app(LicenseService::class);
                $token = $service->generateStatusToken('active', session('tmp_license_key'));
                \App\Models\Setting::updateOrCreate(['key' => 'license_status_token'], ['value' => $token]);
                
                session()->forget(['tmp_license_key', 'tmp_license_status', 'tmp_license_expires_at', 'tmp_license_grace_until', 'tmp_license_customer_name', 'tmp_license_customer_email']);
            }
            // Attempt to register Scheduler (Cron/Task Scheduler)
            $cronResult = \App\Helpers\CronHelper::register();
            \App\Models\Setting::updateOrCreate(['key' => 'scheduler_auto_registered'], ['value' => $cronResult ? 'true' : 'false']);
            
            return response()->json(['success' => true, 'scheduler_automatic' => $cronResult]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
