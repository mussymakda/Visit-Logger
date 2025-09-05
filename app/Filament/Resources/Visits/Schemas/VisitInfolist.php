<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class VisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visit Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Interior Designer'),
                        
                        TextEntry::make('sponsor.name')
                            ->label('Sponsor'),
                        
                        TextEntry::make('sponsor.company_name')
                            ->label('Company'),
                        
                        TextEntry::make('visited_at')
                            ->label('Visit Date & Time')
                            ->dateTime(),
                        
                        TextEntry::make('created_at')
                            ->label('Logged At')
                            ->dateTime(),
                    ])
                    ->columns(2),
                
                Section::make('Visit Details')
                    ->schema([
                        ImageEntry::make('photo')
                            ->label('Site Photo')
                            ->disk('public')
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes(['style' => 'max-height: 400px; object-fit: contain;'])
                            ->columnSpanFull(),
                        
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes provided')
                            ->prose() // For better text formatting
                            ->columnSpanFull(),
                        
                        TextEntry::make('visit_location')
                            ->label('Visit Location')
                            ->placeholder('Location not specified')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Sponsor Information')
                    ->schema([
                        TextEntry::make('sponsor.contact')
                            ->label('Contact Information'),
                        
                        TextEntry::make('sponsor.location')
                            ->label('Sponsor Location'),
                        
                        TextEntry::make('sponsor.description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
