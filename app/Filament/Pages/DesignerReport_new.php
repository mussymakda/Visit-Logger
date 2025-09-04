<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class DesignerReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Designer Report';
    
    protected static ?int $navigationSort = 5;
    
    protected static string $view = 'filament.pages.designer-report';
    
    public function mount()
    {
        $this->designers = $this->getDesignersData();
        $this->summary = $this->getSummaryData();
    }
    
    public $designers = [];
    public $summary = [];
    
    protected function getDesignersData()
    {
        return User::query()
            ->where('role', 'interior_designer')
            ->leftJoin('visits', 'users.id', '=', 'visits.user_id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(visits.id) as total_visits'),
                DB::raw('COUNT(DISTINCT visits.sponsor_id) as unique_sponsors'),
                DB::raw('MIN(visits.created_at) as first_visit'),
                DB::raw('MAX(visits.created_at) as last_visit'),
            ])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_visits')
            ->get();
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
