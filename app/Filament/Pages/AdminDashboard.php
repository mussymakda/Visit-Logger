<?php

namespace App\Filament\Pages;

use App\Models\Visit;
use App\Models\Sponsor;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminDashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Admin Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Livewire\DashboardStatsWidget::class,
            \App\Livewire\RecentVisitsWidget::class,
        ];
    }
}
