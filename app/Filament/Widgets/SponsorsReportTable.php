<?php

namespace App\Filament\Widgets;

use App\Models\Sponsor;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class SponsorsReportTable extends TableWidget
{
    protected static ?string $heading = 'Sponsors Analytics Dashboard';
    protected static ?string $description = 'Comprehensive sponsor database with visit statistics and engagement analytics';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Sponsor::query()->withCount(['visits', 'visits as recent_visits_count' => function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }]))
            ->columns([
                TextColumn::make('name')
                    ->label('Sponsor Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('slate'),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->color('slate')
                    ->placeholder('No company'),
                TextColumn::make('contact')
                    ->label('Contact Info')
                    ->searchable()
                    ->color('gray')
                    ->placeholder('No contact'),
                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('No location'),
                TextColumn::make('visits_count')
                    ->label('Total Visits')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'warning',
                        $state >= 1 => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('recent_visits_count')
                    ->label('Recent Visits (30d)')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 5 => 'success',
                        $state >= 2 => 'warning',
                        $state >= 1 => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y • g:i A')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                Filter::make('location')
                    ->label('Location Filter')
                    ->form([
                        TextInput::make('location')
                            ->label('Location contains')
                            ->placeholder('Enter location to search'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['location'],
                            fn (Builder $query, $location): Builder => $query->where('location', 'like', "%{$location}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['location']) {
                            return 'Location: ' . $data['location'];
                        }
                        return null;
                    }),
                Filter::make('visits_range')
                    ->label('📊 Visit Count Filter')
                    ->form([
                        Select::make('visits_filter')
                            ->label('Filter by visit count')
                            ->options([
                                'high' => 'High activity (10+ visits)',
                                'medium' => 'Medium activity (5-9 visits)',
                                'low' => 'Low activity (1-4 visits)',
                                'none' => 'No visits yet',
                            ])
                            ->placeholder('Select activity level'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['visits_filter'],
                            function (Builder $query, $filter): Builder {
                                return match($filter) {
                                    'high' => $query->having('visits_count', '>=', 10),
                                    'medium' => $query->having('visits_count', '>=', 5)->having('visits_count', '<', 10),
                                    'low' => $query->having('visits_count', '>=', 1)->having('visits_count', '<', 5),
                                    'none' => $query->having('visits_count', '=', 0),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['visits_filter']) {
                            return match($data['visits_filter']) {
                                'high' => 'High activity sponsors',
                                'medium' => 'Medium activity sponsors',
                                'low' => 'Low activity sponsors',
                                'none' => 'Inactive sponsors',
                                default => null,
                            };
                        }
                        return null;
                    }),
                Filter::make('registration_date')
                    ->label('📅 Registration Date')
                    ->form([
                        DatePicker::make('registered_from')
                            ->label('Registered from'),
                        DatePicker::make('registered_until')
                            ->label('Registered until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['registered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['registered_from'] ?? null) {
                            $indicators['from'] = 'Registered from ' . \Carbon\Carbon::parse($data['registered_from'])->toFormattedDateString();
                        }
                        if ($data['registered_until'] ?? null) {
                            $indicators['until'] = 'Registered until ' . \Carbon\Carbon::parse($data['registered_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('📊 Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $sponsors = Sponsor::withCount('visits')->get();
                        $filename = 'sponsors_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
                        
                        return response()->streamDownload(function () use ($sponsors) {
                            $csv = fopen('php://output', 'w');
                            
                            // Add CSV headers
                            fputcsv($csv, [
                                'Sponsor Name',
                                'Company',
                                'Contact',
                                'Location',
                                'Total Visits',
                                'Registration Date',
                            ]);
                            
                            foreach ($sponsors as $sponsor) {
                                fputcsv($csv, [
                                    $sponsor->name,
                                    $sponsor->company_name ?? 'N/A',
                                    $sponsor->contact ?? 'N/A',
                                    $sponsor->location ?? 'N/A',
                                    $sponsor->visits_count,
                                    $sponsor->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }
                            
                            fclose($csv);
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        ]);
                    }),
            ])
            ->recordActions([
                Action::make('view_visits')
                    ->label('View Visits')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => \App\Filament\Pages\AllVisitsReport::getUrl(['sponsor_id' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('sponsor_details')
                    ->label('Sponsor Details')
                    ->icon('heroicon-o-information-circle')
                    ->color('primary')
                    ->modalHeading('Sponsor Details')
                    ->modalContent(fn ($record) => view('filament.sponsor-details-modal', ['sponsor' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $sponsors = Sponsor::withCount(['visits', 'visits as recent_visits_count' => function($query) {
                            $query->where('created_at', '>=', now()->subDays(30));
                        }])->get();
                        
                        $csvData = [];
                        $csvData[] = ['Sponsor Name', 'Company', 'Contact', 'Location', 'Total Visits', 'Recent Visits (30d)', 'Registration Date'];
                        
                        foreach ($sponsors as $sponsor) {
                            $csvData[] = [
                                $sponsor->name ?? 'N/A',
                                $sponsor->company_name ?? 'N/A',
                                $sponsor->contact ?? 'N/A',
                                $sponsor->location ?? 'N/A',
                                $sponsor->visits_count,
                                $sponsor->recent_visits_count,
                                $sponsor->created_at->format('M j, Y • g:i A'),
                            ];
                        }
                        
                        $filename = 'sponsors_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
                        
                        $callback = function() use ($csvData) {
                            $file = fopen('php://output', 'w');
                            foreach ($csvData as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                        };
                        
                        return Response::stream($callback, 200, [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        ]);
                    }),
            ])
            ->defaultSort('visits_count', 'desc')
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateHeading('No sponsors registered yet')
            ->emptyStateDescription('When sponsors are added to the system, they will appear here with their visit statistics.');
    }
}
