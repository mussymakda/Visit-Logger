<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Visit;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DesignerReport extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string $view = 'filament.pages.designer-report';
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Designer Report';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $title = 'Designer Report';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getDesignerReportQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Designer Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('total_visits')
                    ->label('Total Visits')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                    
                TextColumn::make('unique_sponsors')
                    ->label('Unique Sponsors')
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

    protected function getDesignerReportQuery(): Builder
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
            ->havingRaw('COUNT(visits.id) >= 0');
    }

    protected function exportToExcel()
    {
        $data = $this->getDesignerReportQuery()->get();
        
        $filename = 'designer-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Designer Name',
                'Email',
                'Total Visits',
                'Unique Sponsors',
                'First Visit',
                'Last Visit',
            ]);
            
            // Add data rows
            foreach ($data as $designer) {
                fputcsv($file, [
                    $designer->name,
                    $designer->email,
                    $designer->total_visits,
                    $designer->unique_sponsors,
                    $designer->first_visit ? $designer->first_visit->format('Y-m-d H:i:s') : '',
                    $designer->last_visit ? $designer->last_visit->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
