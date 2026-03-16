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
        
        $color = match ($status) {
            'active' => 'success',
            'expired' => 'danger',
            'suspended' => 'warning',
            default => 'gray',
        };

        return [
            Stat::make('License Status', strtoupper($status))
                ->description($expiresAt ? "Expires: $expiresAt" : 'Please activate your license')
                ->descriptionIcon($status === 'active' ? 'heroicon-m-check-badge' : 'heroicon-m-x-circle')
                ->color($color),
        ];
    }
}
