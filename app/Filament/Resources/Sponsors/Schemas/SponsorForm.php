<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('company_name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),
                
                FileUpload::make('logo')
                    ->label('Company Logo')
                    ->image()
                    ->directory('sponsor-logos')
                    ->disk('public')
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('200')
                    ->imageResizeTargetHeight('200')
                    ->helperText('Optional: Upload company logo (will appear on QR code PDF)')
                    ->nullable(),
                
                TextInput::make('google_reviews_link')
                    ->label('Google Reviews Link')
                    ->url()
                    ->placeholder('https://g.page/...')
                    ->helperText('Optional: Link to Google Reviews page for this sponsor')
                    ->maxLength(500),
                
                TextInput::make('contact')
                    ->label('Contact Information')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
