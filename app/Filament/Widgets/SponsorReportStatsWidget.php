<?php

namespace App\Filament\Widgets;

use App\Models\Sponsor;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SponsorReportStatsWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSponsors = Sponsor::count();
        $activeSponsors = Sponsor::whereHas('visits')->count();
        $totalVisits = Visit::count();

        return [
            Stat::make('Total Sponsors', $totalSponsors)
                ->description('All registered sponsors')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Active Sponsors', $activeSponsors)
                ->description('Sponsors with visits')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Visits', $totalVisits)
                ->description('All sponsor visits')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Average Visits', $totalSponsors > 0 ? number_format($totalVisits / $totalSponsors, 1) : '0')
                ->description('Per sponsor')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
