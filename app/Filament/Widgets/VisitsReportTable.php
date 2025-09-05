<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class VisitsReportTable extends TableWidget
{
    protected static ?string $heading = 'Visit Analytics Dashboard';

    protected static ?string $description = 'Complete overview of all designer visits with comprehensive filtering and analytics';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Visit::query()->with(['user', 'sponsor']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Interior Designer')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('slate'),
                TextColumn::make('sponsor.name')
                    ->label('Sponsor Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('slate'),
                TextColumn::make('sponsor.company_name')
                    ->label('Company')
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),
                TextColumn::make('sponsor.location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('Visit Date & Time')
                    ->dateTime('M j, Y • g:i A')
                    ->sortable()
                    ->color('gray'),
                ImageColumn::make('photo')
                    ->label('Visit Photo')
                    ->disk('public')
                    ->size(80)
                    ->circular()
                    ->defaultImageUrl('/images/no-photo.png'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filter by Designer')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('sponsor_id')
                    ->label('Filter by Sponsor')
                    ->relationship('sponsor', 'name')
                    ->preload()
                    ->searchable(),
                Filter::make('date_range')
                    ->label('Date Range Filter')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Select start date'),
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From '.\Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until '.\Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('view_photo')
                    ->label('View Photo')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('gray')
                    ->visible(fn ($record) => ! empty($record->photo))
                    ->url(fn ($record) => asset('storage/'.$record->photo))
                    ->openUrlInNewTab(),
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Visit Details')
                    ->modalContent(fn ($record) => view('filament.visit-details-modal', ['visit' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $visits = Visit::with(['user', 'sponsor'])->get();
                        
                        $csvData = [];
                        $csvData[] = ['Designer Name', 'Sponsor Name', 'Company', 'Location', 'Visit Date', 'Visit Time'];
                        
                        foreach ($visits as $visit) {
                            $csvData[] = [
                                $visit->user->name ?? 'N/A',
                                $visit->sponsor->name ?? 'N/A',
                                $visit->sponsor->company_name ?? 'N/A',
                                $visit->sponsor->location ?? 'N/A',
                                $visit->created_at->format('M j, Y'),
                                $visit->created_at->format('g:i A'),
                            ];
                        }
                        
                        $filename = 'visits_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
                        
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
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->emptyStateIcon('heroicon-o-camera')
            ->emptyStateHeading('No visits recorded yet')
            ->emptyStateDescription('When designers start visiting sponsors, their records will appear here.');
    }
}
