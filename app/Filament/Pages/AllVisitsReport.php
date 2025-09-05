<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\VisitsReportTable;
use Filament\Pages\Page;

class AllVisitsReport extends Page
{
    protected static ?string $title = 'All Visits Report';
    protected static ?string $navigationLabel = 'All Visits';
    protected static ?int $navigationSort = 1;

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VisitsReportTable::class,
        ];
    }
}
