<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class SponsorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name'),
                    
                TextEntry::make('company_name')
                    ->label('Company Name'),
                    
                TextEntry::make('contact')
                    ->label('Contact'),
                    
                TextEntry::make('location')
                    ->label('Location'),
                    
                TextEntry::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                    
                ImageEntry::make('qr_code_path')
                    ->label('QR Code')
                    ->disk('local')
                    ->visibility('public')
                    ->size(200),
                    
                TextEntry::make('qr_code')
                    ->label('QR Code Data')
                    ->copyable()
                    ->columnSpanFull(),
            ]);
    }
}
