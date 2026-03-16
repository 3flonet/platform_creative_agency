<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class TopPagesWidget extends TableWidget
{
    protected static ?string $heading = 'Most Visited Pages';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'half';

    public string $period = '7';

    #[On('traffic-period-updated')]
    public function syncPeriod(string $period): void
    {
        $this->period = $period;
    }

    protected function getTableData(): \Illuminate\Support\Collection
    {
        $now   = now();
        $query = DB::table('site_visits');

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

        return $query
            ->select(
                'url',
                DB::raw('COUNT(*) as views_count'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_count')
            )
            ->groupBy('url')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get()
            ->map(fn ($item) => (array) $item);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getTableData())
            ->columns([
                TextColumn::make('url')
                    ->label('Page Path')
                    ->formatStateUsing(fn ($state) => parse_url($state, PHP_URL_PATH) ?: '/')
                    ->description(fn ($record) => $record['url'])
                    ->wrap(),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('unique_count')
                    ->label('Visitors')
                    ->numeric()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
