<?php

namespace App\Livewire;

use App\Models\Visit;
use App\Models\Sponsor;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalVisits = Visit::count();
        $totalSponsors = Sponsor::count();
        $totalDesigners = User::where('role', 'interior_designer')->count();
        $todayVisits = Visit::whereDate('created_at', today())->count();
        $thisWeekVisits = Visit::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthVisits = Visit::whereMonth('created_at', now()->month)->count();

        // Most active designer
        $topDesigner = Visit::select('user_id', DB::raw('count(*) as visit_count'))
            ->groupBy('user_id')
            ->orderByDesc('visit_count')
            ->with('user')
            ->first();

        // Most visited sponsor
        $topSponsor = Visit::select('sponsor_id', DB::raw('count(*) as visit_count'))
            ->groupBy('sponsor_id')
            ->orderByDesc('visit_count')
            ->with('sponsor')
            ->first();

        return [
            Stat::make('Total Visits', $totalVisits)
                ->description('All time visits')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('success'),
            
            Stat::make('Today Visits', $todayVisits)
                ->description('Visits logged today')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info'),
            
            Stat::make('Interior Designers', $totalDesigners)
                ->description('Registered designers')
                ->descriptionIcon('heroicon-o-users')
                ->color('secondary'),
            
           
        ];
    }
}
