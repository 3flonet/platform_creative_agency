<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Observers\ProjectObserver;
use App\Observers\ServiceObserver;
use App\Observers\TeamMemberObserver;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Register model observers for image optimization
        Project::observe(ProjectObserver::class);
        Service::observe(ServiceObserver::class);
        TeamMember::observe(TeamMemberObserver::class);

        FilamentAsset::register([
            Css::make('uicons', asset('vendor/uicons/css/uicons-solid-rounded.css')),
        ]);

        // Dynamic Mail Configuration from Database
        if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
            try {
                if (file_exists(storage_path('installed.lock'))) {
                    $mailSettings = \App\Models\Setting::where('key', 'like', 'mail_%')->pluck('value', 'key');
                    
                    if ($mailSettings->isNotEmpty()) {
                        config([
                            'mail.mailers.smtp.transport' => $mailSettings->get('mail_mailer', config('mail.mailers.smtp.transport')),
                            'mail.mailers.smtp.host' => $mailSettings->get('mail_host', config('mail.mailers.smtp.host')),
                            'mail.mailers.smtp.port' => $mailSettings->get('mail_port', config('mail.mailers.smtp.port')),
                            'mail.mailers.smtp.encryption' => $mailSettings->get('mail_encryption', config('mail.mailers.smtp.encryption')),
                            'mail.mailers.smtp.username' => $mailSettings->get('mail_username', config('mail.mailers.smtp.username')),
                            'mail.mailers.smtp.password' => $mailSettings->get('mail_password', config('mail.mailers.smtp.password')),
                            'mail.from.address' => $mailSettings->get('mail_from_address', config('mail.from.address')),
                            'mail.from.name' => $mailSettings->get('mail_from_name', config('mail.from.name')),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Fail silently if DB not ready
            }
        }
    }
}
