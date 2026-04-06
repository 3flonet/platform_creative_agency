<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Setting;

class LicenseStatusWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $status = Setting::get('license_status', 'inactive');
        $expiresAt = Setting::get('license_expires_at');
        $graceUntil = Setting::get('license_grace_until');
        
        $color = match ($status) {
            'active' => 'success',
            'expired' => 'danger',
            'suspended', 'revoked' => 'danger',
            'tampered' => 'warning',
            default => 'gray',
        };

        $description = 'Please activate your license';
        if ($expiresAt) {
            $description = "Expires: $expiresAt";
            if ($status === 'expired' && $graceUntil) {
                $description = "Expired! Grace period until: $graceUntil";
            }
        }

        return [
            Stat::make('License Status', strtoupper($status))
                ->description($description)
                ->descriptionIcon($status === 'active' ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-triangle')
                ->color($color),
        ];
    }
}
