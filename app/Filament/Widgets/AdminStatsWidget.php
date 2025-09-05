<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Sponsor;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalDesigners = User::where('role', 'interior_designer')->count();
        $totalSponsors = Sponsor::count();
        $totalVisits = Visit::count();
        $todayVisits = Visit::whereDate('created_at', today())->count();
        $thisMonthVisits = Visit::whereMonth('created_at', now()->month)->count();
        $lastMonthVisits = Visit::whereMonth('created_at', now()->subMonth()->month)->count();
        $visitGrowth = $lastMonthVisits > 0 ? (($thisMonthVisits - $lastMonthVisits) / $lastMonthVisits * 100) : 0;

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 12, 18, 22, 15, 28, $totalUsers]),
            
            Stat::make('Interior Designers', number_format($totalDesigners))
                ->description('Active designers')
                ->descriptionIcon('heroicon-m-paint-brush')
                ->color('success')
                ->chart([3, 6, 9, 12, 15, 18, $totalDesigners]),
            
            Stat::make('Sponsors', number_format($totalSponsors))
                ->description('Registered sponsors')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 22, $totalSponsors]),
            
            Stat::make('Total Visits', number_format($totalVisits))
                ->description($visitGrowth >= 0 ? 
                    number_format($visitGrowth, 1) . '% increase from last month' : 
                    number_format(abs($visitGrowth), 1) . '% decrease from last month')
                ->descriptionIcon($visitGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($visitGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    max(1, $lastMonthVisits - 5), 
                    max(1, $lastMonthVisits - 2), 
                    $lastMonthVisits, 
                    max(1, ($lastMonthVisits + $thisMonthVisits) / 2), 
                    $thisMonthVisits,
                    $thisMonthVisits + 2,
                    $thisMonthVisits + 3
                ]),
        ];
    }
}
