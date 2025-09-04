<?php

namespace App\Filament\Designer\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Icons\Heroicon;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

class VisitHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'My Visits';
    
    protected static ?string $title = 'Visit History';
    
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.designer.pages.visit-history';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Visit::query()
                    ->where('user_id', auth()->id())
                    ->with(['sponsor'])
            )
            ->columns([
                TextColumn::make('sponsor.name')
                    ->label('Sponsor')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('sponsor.company_name')
                    ->label('Company')
                    ->searchable(),
                    
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
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('visited_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
