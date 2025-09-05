<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ReportsStatsWidget;
use Filament\Pages\Dashboard;

class Reports extends Dashboard
{
    protected static ?string $title = 'Reports Dashboard';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?int $navigationSort = 4;

    public function getWidgets(): array
    {
        return [
            ReportsStatsWidget::class,
            \App\Filament\Widgets\VisitsReportTable::class,
        ];
    }
}
