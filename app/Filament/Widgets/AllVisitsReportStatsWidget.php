<?php

namespace App\Filament\Widgets;

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AllVisitsReportStatsWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalVisits = Visit::count();
        $totalDesigners = User::where('role', 'interior_designer')->count();
        $totalSponsors = Sponsor::count();
        $todayVisits = Visit::whereDate('created_at', today())->count();

        return [
            Stat::make('Total Visits', $totalVisits)
                ->description('All time visits')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),
            Stat::make('Today\'s Visits', $todayVisits)
                ->description('Visits made today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
            Stat::make('Total Designers', $totalDesigners)
                ->description('Registered designers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Total Sponsors', $totalSponsors)
                ->description('Registered sponsors')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning'),
        ];
    }
}
