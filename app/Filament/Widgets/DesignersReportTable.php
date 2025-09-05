<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class DesignersReportTable extends TableWidget
{
    protected static ?string $heading = 'Interior Designers Analytics';
    protected static ?string $description = 'Complete designer directory with activity tracking and performance insights';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->where('role', 'interior_designer')->withCount([
                'visits',
                'visits as recent_visits_count' => function($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                },
                'visits as this_month_visits_count' => function($query) {
                    $query->whereMonth('created_at', now()->month);
                }
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Designer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('slate'),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->tooltip('Click to copy'),
                TextColumn::make('visits_count')
                    ->label('Total Visits')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 20 => 'success',
                        $state >= 10 => 'warning',
                        $state >= 5 => 'info',
                        $state >= 1 => 'gray',
                        default => 'danger',
                    }),
                TextColumn::make('recent_visits_count')
                    ->label('Recent Activity (30d)')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'warning',
                        $state >= 2 => 'info',
                        $state >= 1 => 'gray',
                        default => 'danger',
                    }),
                TextColumn::make('this_month_visits_count')
                    ->label('This Month')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 5 => 'success',
                        $state >= 3 => 'warning',
                        $state >= 1 => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Joined Platform')
                    ->dateTime('M j, Y • g:i A')
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('email_verified_at')
                    ->label('Account Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Unverified')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
            ])
            ->filters([
                Filter::make('activity_level')
                    ->label('Activity Level')
                    ->form([
                        Select::make('activity')
                            ->label('Filter by activity level')
                            ->options([
                                'very_active' => 'Very Active (20+ visits)',
                                'active' => 'Active (10-19 visits)',
                                'moderate' => 'Moderate (5-9 visits)',
                                'low' => 'Low Activity (1-4 visits)',
                                'inactive' => 'Inactive (0 visits)',
                            ])
                            ->placeholder('Select activity level'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['activity'],
                            function (Builder $query, $activity): Builder {
                                return match($activity) {
                                    'very_active' => $query->having('visits_count', '>=', 20),
                                    'active' => $query->having('visits_count', '>=', 10)->having('visits_count', '<', 20),
                                    'moderate' => $query->having('visits_count', '>=', 5)->having('visits_count', '<', 10),
                                    'low' => $query->having('visits_count', '>=', 1)->having('visits_count', '<', 5),
                                    'inactive' => $query->having('visits_count', '=', 0),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['activity']) {
                            return match($data['activity']) {
                                'very_active' => 'Very active designers',
                                'active' => 'Active designers',
                                'moderate' => 'Moderately active designers',
                                'low' => 'Low activity designers',
                                'inactive' => 'Inactive designers',
                                default => null,
                            };
                        }
                        return null;
                    }),
                SelectFilter::make('email_verified_at')
                    ->label('🔐 Account Status')
                    ->options([
                        'verified' => 'Verified accounts',
                        'unverified' => 'Unverified accounts',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'verified',
                            fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'),
                        )->when(
                            $data['value'] === 'unverified',
                            fn (Builder $query): Builder => $query->whereNull('email_verified_at'),
                        );
                    }),
                Filter::make('join_date')
                    ->label('Join Date Range')
                    ->form([
                        DatePicker::make('joined_from')
                            ->label('Joined from'),
                        DatePicker::make('joined_until')
                            ->label('Joined until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['joined_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['joined_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['joined_from'] ?? null) {
                            $indicators['from'] = 'Joined from ' . \Carbon\Carbon::parse($data['joined_from'])->toFormattedDateString();
                        }
                        if ($data['joined_until'] ?? null) {
                            $indicators['until'] = 'Joined until ' . \Carbon\Carbon::parse($data['joined_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $designers = User::where('role', 'interior_designer')->withCount('visits')->get();
                        $filename = 'designers_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
                        
                        return response()->streamDownload(function () use ($designers) {
                            $csv = fopen('php://output', 'w');
                            
                            // Add CSV headers
                            fputcsv($csv, [
                                'Designer Name',
                                'Email',
                                'Total Visits',
                                'Account Status',
                                'Join Date',
                                'Last Activity',
                            ]);
                            
                            foreach ($designers as $designer) {
                                $lastVisit = $designer->visits()->latest()->first();
                                fputcsv($csv, [
                                    $designer->name,
                                    $designer->email,
                                    $designer->visits_count,
                                    $designer->email_verified_at ? 'Verified' : 'Unverified',
                                    $designer->created_at->format('Y-m-d H:i:s'),
                                    $lastVisit ? $lastVisit->created_at->format('Y-m-d H:i:s') : 'No visits',
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
                    ->url(fn ($record) => \App\Filament\Pages\AllVisitsReport::getUrl(['designer_id' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('designer_details')
                    ->label('Designer Profile')
                    ->icon('heroicon-o-user-circle')
                    ->color('primary')
                    ->modalHeading('Designer Profile')
                    ->modalContent(fn ($record) => view('filament.designer-details-modal', ['designer' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $designers = User::where('role', 'interior_designer')->withCount([
                            'visits',
                            'visits as recent_visits_count' => function($query) {
                                $query->where('created_at', '>=', now()->subDays(30));
                            },
                            'visits as this_month_visits_count' => function($query) {
                                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                            }
                        ])->get();
                        
                        $csvData = [];
                        $csvData[] = ['Designer Name', 'Email', 'Total Visits', 'Recent Activity (30d)', 'This Month', 'Joined Platform', 'Account Status'];
                        
                        foreach ($designers as $designer) {
                            $csvData[] = [
                                $designer->name ?? 'N/A',
                                $designer->email ?? 'N/A',
                                $designer->visits_count,
                                $designer->recent_visits_count,
                                $designer->this_month_visits_count,
                                $designer->created_at->format('M j, Y • g:i A'),
                                $designer->email_verified_at ? 'Verified' : 'Unverified',
                            ];
                        }
                        
                        $filename = 'designers_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
                        
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
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No designers registered yet')
            ->emptyStateDescription('When interior designers join the platform, their profiles and activity will appear here.');
    }
}
