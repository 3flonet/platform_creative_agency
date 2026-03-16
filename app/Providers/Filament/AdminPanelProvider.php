<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use App\Filament\Pages\Auth\EditProfile;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $lockFile = storage_path('installed.lock');
        $isInstalled = file_exists($lockFile);
        $isLicenseActive = false;

        if ($isInstalled) {
            $isLicenseActive = Setting::get('license_status') === 'active';
        }

        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class)
            ->brandName(fn () => $isInstalled ? (Setting::get('site_name') ?? '3FLO') : '3FLO')
            ->brandLogo(fn () => 
                $isInstalled && Setting::get('site_logo') 
                    ? asset('storage/' . Setting::get('site_logo'))
                    : null
            )
            ->favicon(fn () => 
                $isInstalled && Setting::get('site_favicon') 
                    ? asset('storage/' . Setting::get('site_favicon'))
                    : null
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth(Width::Full);

        if ($isLicenseActive) {
            $panel
                ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
                ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
                ->pages([
                    Dashboard::class,
                ])
                ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
                ->widgets([]);
        } else {
            $panel
                ->pages([
                    \App\Filament\Pages\LicenseActivation::class,
                ])
                ->widgets([
                    \App\Filament\Widgets\LicenseStatusWidget::class,
                ]);
        }

        return $panel
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
