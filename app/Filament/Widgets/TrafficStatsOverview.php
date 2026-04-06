<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class TrafficStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.traffic-stats-overview';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public string $period = '7';

    /**
     * When $period changes (via the blade select), dispatch event to all traffic widgets.
     */
    public function updatedPeriod(string $value): void
    {
        $this->dispatch('traffic-period-updated', period: $value);
    }

    public function getStats(): array
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
            default      => null, // 'all' — no filter
        };

        $totalViews     = (clone $query)->count();
        $uniqueVisitors = (clone $query)->where('is_unique', true)->count();

        return [
            'views'    => $totalViews,
            'visitors' => $uniqueVisitors,
            'label'    => match ($this->period) {
                'today'      => 'Today',
                'yesterday'  => 'Yesterday',
                '7'          => 'Last 7 Days',
                '30'         => 'Last 30 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'all'        => 'All Time',
                default      => 'Selected Period',
            },
        ];
    }
}
