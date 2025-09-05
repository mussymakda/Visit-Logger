<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DesignerReportStatsWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalDesigners = User::where('role', 'interior_designer')->count();
        $activeDesigners = User::where('role', 'interior_designer')
            ->whereHas('visits')
            ->count();
        $totalVisits = Visit::whereHas('user', function ($query) {
            $query->where('role', 'interior_designer');
        })->count();

        return [
            Stat::make('Total Designers', $totalDesigners)
                ->description('All registered designers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Active Designers', $activeDesigners)
                ->description('Designers with visits')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Visits', $totalVisits)
                ->description('All designer visits')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Average Visits', $totalDesigners > 0 ? number_format($totalVisits / $totalDesigners, 1) : '0')
                ->description('Per designer')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
