<?php

namespace App\Filament\Resources\Sponsors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('contact')
                    ->searchable(),
                    
                TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                    
                ImageColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->disk('local')
                    ->visibility('public')
                    ->size(60),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('downloadQR')
                    ->label('Download QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->qr_code_path ? Storage::url($record->qr_code_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->qr_code_path !== null),
                    
                Action::make('regenerateQR')
                    ->label('Regenerate QR')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn ($record) => $record->generateQrCode())
                    ->requiresConfirmation()
                    ->modalDescription('This will generate a new QR code for this sponsor.'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
