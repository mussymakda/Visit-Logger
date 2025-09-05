<?php

namespace App\Filament\Widgets;

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalVisits = Visit::count();
        $thisMonthVisits = Visit::whereMonth('created_at', now()->month)->count();
        $lastMonthVisits = Visit::whereMonth('created_at', now()->subMonth()->month)->count();
        $visitGrowth = $lastMonthVisits > 0 ? (($thisMonthVisits - $lastMonthVisits) / $lastMonthVisits * 100) : 0;

        $totalSponsors = Sponsor::count();
        $activeSponsors = Sponsor::whereHas('visits', function ($query) {
            $query->whereMonth('created_at', now()->month);
        })->count();

        $totalDesigners = User::where('role', 'interior_designer')->count();
        $activeDesigners = User::where('role', 'interior_designer')
            ->whereHas('visits', function ($query) {
                $query->whereMonth('created_at', now()->month);
            })->count();

        $todayVisits = Visit::whereDate('created_at', today())->count();

        return [
            Stat::make('📊 Total Visits', number_format($totalVisits))
                ->description('All time visit records')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('success')
                ->chart([7, 12, 18, 22, 15, 28, $totalVisits > 30 ? 35 : $totalVisits]),

            Stat::make('🏢 Active Sponsors', number_format($activeSponsors).' / '.number_format($totalSponsors))
                ->description('Sponsors with visits this month')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 22, $activeSponsors]),

            Stat::make('🎨 Active Designers', number_format($activeDesigners).' / '.number_format($totalDesigners))
                ->description('Designers active this month')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->chart([3, 6, 9, 12, 15, 18, $activeDesigners]),

            Stat::make('📅 Monthly Performance', number_format($thisMonthVisits))
                ->description($visitGrowth >= 0 ?
                    number_format($visitGrowth, 1).'% increase from last month' :
                    number_format(abs($visitGrowth), 1).'% decrease from last month')
                ->descriptionIcon($visitGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($visitGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    max(1, $lastMonthVisits - 5),
                    max(1, $lastMonthVisits - 2),
                    $lastMonthVisits,
                    max(1, ($lastMonthVisits + $thisMonthVisits) / 2),
                    $thisMonthVisits,
                    $thisMonthVisits + 2,
                    $thisMonthVisits + 3,
                ]),
        ];
    }
}
