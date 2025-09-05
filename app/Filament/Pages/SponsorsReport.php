<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SponsorsReportTable;
use Filament\Pages\Page;

class SponsorsReport extends Page
{
    protected static ?string $title = 'Sponsors Report';
    protected static ?string $navigationLabel = 'All Sponsors';
    protected static ?int $navigationSort = 2;

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SponsorsReportTable::class,
        ];
    }
}
