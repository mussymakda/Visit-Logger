<?php

namespace App\Filament\Designer\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-viewfinder-circle';
    
    protected static ?string $navigationLabel = 'QR Scanner';
    
    protected static ?string $title = 'QR Code Scanner';
    
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.designer.pages.dashboard';
    
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
