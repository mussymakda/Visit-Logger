<?php

namespace App\Livewire;

use App\Models\Visit;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentVisitsWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Visit::query()
                    ->with(['user', 'sponsor'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Designer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sponsor.name')
                    ->label('Sponsor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->disk('public')
                    ->visibility('public')
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Visit Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->paginated(false);
    }

    protected function getTableHeading(): string
    {
        return 'Recent Visits';
    }
}
