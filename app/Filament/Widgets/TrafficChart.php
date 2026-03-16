<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class TrafficChart extends ChartWidget
{
    protected ?string $heading = 'Site Traffic';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    // period is synced from TrafficStatsOverview via event
    public string $period = '7';

    /**
     * Listen to the master filter event from TrafficStatsOverview
     */
    #[On('traffic-period-updated')]
    public function syncPeriod(string $period): void
    {
        $this->period = $period;
    }

    // No getFilters() — the native Filament filter is intentionally removed.
    // All filtering is controlled by TrafficStatsOverview widget.

    protected function getData(): array
    {
        $now = now();
        $endDate = $now->copy()->endOfDay();

        if ($this->period === 'today') {
            $startDate = $now->copy()->startOfDay();
            $groupBy   = 'hour';
        } elseif ($this->period === 'yesterday') {
            $startDate = $now->copy()->subDay()->startOfDay();
            $endDate   = $now->copy()->subDay()->endOfDay();
            $groupBy   = 'hour';
        } elseif ($this->period === 'this_month') {
            $startDate = $now->copy()->startOfMonth();
            $groupBy   = 'day';
        } elseif ($this->period === 'last_month') {
            $startDate = $now->copy()->subMonth()->startOfMonth();
            $endDate   = $now->copy()->subMonth()->endOfMonth();
            $groupBy   = 'day';
        } elseif ($this->period === 'all') {
            $firstVisit = SiteVisit::min('created_at');
            $startDate  = $firstVisit ? Carbon::parse($firstVisit)->startOfDay() : $now->copy()->subYear();
            $groupBy    = ($now->diffInMonths($startDate) > 2) ? 'month' : 'day';
        } else {
            // Default: last N days
            $days      = (int) $this->period ?: 7;
            $startDate = $now->copy()->subDays($days - 1)->startOfDay();
            $groupBy   = 'day';
        }

        $query = SiteVisit::select(
                DB::raw("COUNT(*) as total_views"),
                DB::raw("COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_visitors")
            )
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($groupBy === 'hour') {
            $query->addSelect(DB::raw("HOUR(created_at) as time_unit"))->groupBy('time_unit')->orderBy('time_unit');
        } elseif ($groupBy === 'month') {
            $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as time_unit"))->groupBy('time_unit')->orderBy('time_unit');
        } else {
            $query->addSelect(DB::raw("DATE(created_at) as time_unit"))->groupBy('time_unit')->orderBy('time_unit');
        }

        $visits     = $query->get();
        $labels     = [];
        $viewsData  = [];
        $uniqueData = [];

        if ($groupBy === 'hour') {
            for ($i = 0; $i <= 23; $i++) {
                $labels[]     = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $row          = $visits->firstWhere('time_unit', $i);
                $viewsData[]  = $row ? $row->total_views : 0;
                $uniqueData[] = $row ? $row->unique_visitors : 0;
            }
        } elseif ($groupBy === 'month') {
            $current = $startDate->copy()->startOfMonth();
            while ($current <= $endDate) {
                $labels[]     = $current->format('M Y');
                $row          = $visits->firstWhere('time_unit', $current->format('Y-m'));
                $viewsData[]  = $row ? $row->total_views : 0;
                $uniqueData[] = $row ? $row->unique_visitors : 0;
                $current->addMonth();
            }
        } else {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $labels[]     = $current->format('D, d M');
                $row          = $visits->firstWhere('time_unit', $current->format('Y-m-d'));
                $viewsData[]  = $row ? $row->total_views : 0;
                $uniqueData[] = $row ? $row->unique_visitors : 0;
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Total Page Views',
                    'data'            => $viewsData,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Unique Visitors',
                    'data'            => $uniqueData,
                    'borderColor'     => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
