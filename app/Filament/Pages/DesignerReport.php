<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class DesignerReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    
    protected static ?string $navigationLabel = 'Designer Report';
    
    protected static ?int $navigationSort = 5;
    
    protected string $view = 'filament.pages.designer-report';
    
    public function mount()
    {
        $this->designers = $this->getDesignersData();
        $this->summary = $this->getSummaryData();
    }
    
    public $designers = [];
    public $summary = [];
    
    protected function getDesignersData()
    {
        return User::where('role', 'interior_designer')
            ->with(['visits.sponsor'])
            ->get()
            ->map(function ($designer) {
                $visits = $designer->visits;
                $uniqueSponsors = $visits->pluck('sponsor')->unique('id');
                
                return (object) [
                    'id' => $designer->id,
                    'name' => $designer->name,
                    'email' => $designer->email,
                    'total_visits' => $visits->count(),
                    'unique_sponsors_count' => $uniqueSponsors->count(),
                    'sponsors' => $uniqueSponsors->map(function ($sponsor) use ($visits) {
                        $sponsorVisits = $visits->where('sponsor_id', $sponsor->id);
                        return (object) [
                            'name' => $sponsor->name,
                            'company_name' => $sponsor->company_name,
                            'contact' => $sponsor->contact,
                            'location' => $sponsor->location,
                            'visit_count' => $sponsorVisits->count(),
                            'first_visit' => $sponsorVisits->min('created_at'),
                            'last_visit' => $sponsorVisits->max('created_at'),
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
        $totalDesigners = User::where('role', 'interior_designer')->count();
        $activeDesigners = User::where('role', 'interior_designer')
            ->whereHas('visits')
            ->count();
        $totalVisits = DB::table('visits')
            ->join('users', 'visits.user_id', '=', 'users.id')
            ->where('users.role', 'interior_designer')
            ->count();
        
        return [
            'total_designers' => $totalDesigners,
            'active_designers' => $activeDesigners,
            'total_visits' => $totalVisits,
            'avg_visits' => $totalDesigners > 0 ? $totalVisits / $totalDesigners : 0,
        ];
    }
}
