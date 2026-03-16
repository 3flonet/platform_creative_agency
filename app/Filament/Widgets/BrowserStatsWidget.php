<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class BrowserStatsWidget extends ChartWidget
{
    protected ?string $heading = 'Browser Usage';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'half';

    public string $period = '7';

    #[On('traffic-period-updated')]
    public function syncPeriod(string $period): void
    {
        $this->period = $period;
    }

    protected function getData(): array
    {
        $now   = now();
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

        $stats = $query
            ->select('user_agent', DB::raw('count(*) as count'))
            ->groupBy('user_agent')
            ->get();

        $browsers = [
            'Chrome'            => 0,
            'Safari'            => 0,
            'Firefox'           => 0,
            'Edge'              => 0,
            'Mobile App / Other' => 0,
        ];

        foreach ($stats as $stat) {
            $ua = $stat->user_agent;
            if (stripos($ua, 'Edg') !== false) {
                $browsers['Edge'] += $stat->count;
            } elseif (stripos($ua, 'Chrome') !== false) {
                $browsers['Chrome'] += $stat->count;
            } elseif (stripos($ua, 'Safari') !== false) {
                $browsers['Safari'] += $stat->count;
            } elseif (stripos($ua, 'Firefox') !== false) {
                $browsers['Firefox'] += $stat->count;
            } else {
                $browsers['Mobile App / Other'] += $stat->count;
            }
        }

        $browsers = array_filter($browsers, fn ($count) => $count > 0);

        return [
            'datasets' => [
                [
                    'label'           => 'Browsers',
                    'data'            => array_values($browsers),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                ],
            ],
            'labels' => array_keys($browsers),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
