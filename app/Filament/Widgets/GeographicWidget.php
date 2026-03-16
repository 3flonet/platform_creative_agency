<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class GeographicWidget extends Widget
{
    protected string $view = 'filament.widgets.geographic-widget';
    protected static ?string $heading = 'Geographic Data';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public string $period = '7';

    #[On('traffic-period-updated')]
    public function syncPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function getViewData(): array
    {
        $now = now();
        $query = SiteVisit::query();

        match ($this->period) {
            'today'      => $query->whereDate('created_at', $now->toDateString()),
            'yesterday'  => $query->whereDate('created_at', $now->copy()->subDay()->toDateString()),
            '7'          => $query->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay()),
            '30'         => $query->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay()),
            'this_month' => $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year),
            'last_month' => $query->whereMonth('created_at', $now->copy()->subMonth()->month)
                                  ->whereYear('created_at', $now->copy()->subMonth()->year),
            default      => null,
        };

        // Group by country
        $countryStats = (clone $query)->select('country_code', 'country', DB::raw('COUNT(*) as visits'))
            ->whereNotNull('country_code')
            ->groupBy('country_code', 'country')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        // Group by city
        $cityStats = (clone $query)->select('city', 'country_code', DB::raw('COUNT(*) as visits'))
            ->whereNotNull('city')
            ->groupBy('city', 'country_code')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        $totalVisits = $countryStats->sum('visits') ?: 1; // Prevent division by zero

        $countries = $countryStats->map(function ($item) use ($totalVisits) {
            $code = strtolower($item->country_code);
            return [
                'code' => $code,
                'name' => $item->country,
                'visits' => $item->visits,
                'percentage' => round(($item->visits / $totalVisits) * 100, 1),
                'flag' => "https://flagcdn.com/w20/{$code}.png"
            ];
        });

        $cities = $cityStats->map(function ($item) use ($totalVisits) {
            $code = strtolower($item->country_code);
            return [
                'name' => $item->city,
                'country_code' => strtoupper($code),
                'visits' => $item->visits,
                'percentage' => round(($item->visits / $totalVisits) * 100, 1),
                'flag' => "https://flagcdn.com/w20/{$code}.png"
            ];
        });

        return [
            'countries' => $countries,
            'cities' => $cities,
            'totalVisits' => $totalVisits > 1 ? $totalVisits : 0, 
        ];
    }
}
