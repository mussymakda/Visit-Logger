<?php

namespace App\Filament\Pages;

use App\Models\Sponsor;
use App\Models\Visit;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SponsorReport extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string $view = 'filament.pages.sponsor-report';
    
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Sponsor Report';
    
    protected static ?int $navigationSort = 6;
    
    protected static ?string $title = 'Sponsor Report';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getSponsorReportQuery())
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('contact_name')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('total_visits')
                    ->label('Total Visits')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                    
                TextColumn::make('unique_designers')
                    ->label('Unique Designers')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                    
                TextColumn::make('first_visit')
                    ->label('First Visit')
                    ->dateTime()
                    ->sortable(),
                    
                TextColumn::make('last_visit')
                    ->label('Last Visit')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return $this->exportToExcel();
                    }),
            ])
            ->defaultSort('total_visits', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getSponsorReportQuery(): Builder
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
            ->havingRaw('COUNT(visits.id) >= 0');
    }

    protected function exportToExcel()
    {
        $data = $this->getSponsorReportQuery()->get();
        
        $filename = 'sponsor-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Company Name',
                'Contact Person',
                'Email',
                'Phone',
                'Total Visits',
                'Unique Designers',
                'First Visit',
                'Last Visit',
            ]);
            
            // Add data rows
            foreach ($data as $sponsor) {
                fputcsv($file, [
                    $sponsor->company_name,
                    $sponsor->contact_name,
                    $sponsor->email,
                    $sponsor->phone,
                    $sponsor->total_visits,
                    $sponsor->unique_designers,
                    $sponsor->first_visit ? $sponsor->first_visit->format('Y-m-d H:i:s') : '',
                    $sponsor->last_visit ? $sponsor->last_visit->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
