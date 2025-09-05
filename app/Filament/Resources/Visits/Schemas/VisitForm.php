<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('Visit Notes')
                    ->placeholder('Enter any notes about this visit')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }
}
