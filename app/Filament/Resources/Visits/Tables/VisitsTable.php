<?php

namespace App\Filament\Resources\Visits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Designer')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('sponsor.name')
                    ->label('Sponsor')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('sponsor.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                    
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->disk('public')
                    ->visibility('public')
                    ->size(60),
                    
                TextColumn::make('visit_location')
                    ->label('Location')
                    ->searchable(),
                    
                TextColumn::make('visited_at')
                    ->label('Visit Date')
                    ->dateTime()
                    ->sortable(),
                    
                TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('visited_at', 'desc');
    }
}
