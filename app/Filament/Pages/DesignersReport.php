<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DesignersReportTable;
use Filament\Pages\Page;

class DesignersReport extends Page
{
    protected static ?string $title = 'Designers Report';
    protected static ?string $navigationLabel = 'All Designers';
    protected static ?int $navigationSort = 3;

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DesignersReportTable::class,
        ];
    }
}
