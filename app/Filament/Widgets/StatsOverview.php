<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Unread Inquiries', \App\Models\ContactInquiry::where('status', 'new')->count())
                ->description(\App\Models\ContactInquiry::where('status', 'new')->count() > 0 ? 'Needs attention!' : 'All caught up')
                ->descriptionIcon(\App\Models\ContactInquiry::where('status', 'new')->count() > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-check-circle')
                ->color(\App\Models\ContactInquiry::where('status', 'new')->count() > 0 ? 'danger' : 'success'),
            Stat::make('Projects', \App\Models\Project::count())
                ->description('The Works')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            Stat::make('Services', \App\Models\Service::count())
                ->description('The Matrix')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('warning'),
            Stat::make('Journal Articles', \App\Models\Article::count())
                ->description('The Narrative')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
