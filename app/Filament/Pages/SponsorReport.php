<?php

namespace App\Filament\Pages;

use App\Models\Sponsor;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class SponsorReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;
    
    protected static ?string $navigationLabel = 'Sponsor Report';
    
    protected static ?int $navigationSort = 6;
    
    protected string $view = 'filament.pages.sponsor-report';
    
    public function mount()
    {
        $this->sponsors = $this->getSponsorsData();
        $this->summary = $this->getSummaryData();
    }
    
    public $sponsors = [];
    public $summary = [];
    
    protected function getSponsorsData()
    {
        return Sponsor::with(['visits.user'])
            ->get()
            ->map(function ($sponsor) {
                $visits = $sponsor->visits;
                $uniqueDesigners = $visits->pluck('user')->unique('id');
                
                return (object) [
                    'id' => $sponsor->id,
                    'name' => $sponsor->name,
                    'company_name' => $sponsor->company_name,
                    'contact' => $sponsor->contact,
                    'location' => $sponsor->location,
                    'total_visits' => $visits->count(),
                    'unique_designers_count' => $uniqueDesigners->count(),
                    'designers' => $uniqueDesigners->map(function ($designer) use ($visits) {
                        $designerVisits = $visits->where('user_id', $designer->id);
                        return (object) [
                            'name' => $designer->name,
                            'email' => $designer->email,
                            'visit_count' => $designerVisits->count(),
                            'first_visit' => $designerVisits->min('created_at'),
                            'last_visit' => $designerVisits->max('created_at'),
                        ];
                    })->sortByDesc('visit_count'),
                    'first_visit' => $visits->min('created_at'),
                    'last_visit' => $visits->max('created_at'),
                ];
            })
            ->sortByDesc('total_visits');
    }
    
    protected function getSummaryData()
    {
        $totalSponsors = Sponsor::count();
        $activeSponsors = Sponsor::whereHas('visits')->count();
        $totalVisits = DB::table('visits')->count();
        
        return [
            'total_sponsors' => $totalSponsors,
            'active_sponsors' => $activeSponsors,
            'total_visits' => $totalVisits,
            'avg_visits' => $totalSponsors > 0 ? $totalVisits / $totalSponsors : 0,
        ];
    }
}
