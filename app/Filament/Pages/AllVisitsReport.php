<?php

namespace App\Filament\Pages;

use App\Models\Visit;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AllVisitsReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    
    protected static ?string $navigationLabel = 'All Visits';
    
    protected static ?int $navigationSort = 7;
    
    protected string $view = 'filament.pages.all-visits-report';
    
    public function mount()
    {
        $this->visits = $this->getAllVisits();
    }
    
    public $visits = [];
    
    protected function getAllVisits()
    {
        return Visit::with(['user', 'sponsor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
