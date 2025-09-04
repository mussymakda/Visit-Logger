<?php

namespace App\Filament\Pages;

use App\Models\Sponsor;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SponsorReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Sponsor Report';
    
    protected static ?int $navigationSort = 6;
    
    protected static string $view = 'filament.pages.sponsor-report';
    
    public function mount()
    {
        $this->sponsors = $this->getSponsorsData();
        $this->summary = $this->getSummaryData();
    }
    
    public $sponsors = [];
    public $summary = [];
    
    protected function getSponsorsData()
    {
        return Sponsor::query()
            ->leftJoin('visits', 'sponsors.id', '=', 'visits.sponsor_id')
            ->select([
                'sponsors.id',
                'sponsors.company_name',
                'sponsors.contact_name',
                'sponsors.email',
                'sponsors.phone',
                DB::raw('COUNT(visits.id) as total_visits'),
                DB::raw('COUNT(DISTINCT visits.user_id) as unique_designers'),
                DB::raw('MIN(visits.created_at) as first_visit'),
                DB::raw('MAX(visits.created_at) as last_visit'),
            ])
            ->groupBy(
                'sponsors.id', 
                'sponsors.company_name', 
                'sponsors.contact_name', 
                'sponsors.email', 
                'sponsors.phone'
            )
            ->orderByDesc('total_visits')
            ->get();
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
