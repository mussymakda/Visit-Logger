<?php

namespace App\Filament\Resources\InteriorDesigners;

use App\Filament\Resources\InteriorDesigners\Pages\CreateInteriorDesigner;
use App\Filament\Resources\InteriorDesigners\Pages\EditInteriorDesigner;
use App\Filament\Resources\InteriorDesigners\Pages\ListInteriorDesigners;
use App\Filament\Resources\InteriorDesigners\Pages\ViewInteriorDesigner;
use App\Filament\Resources\InteriorDesigners\Schemas\InteriorDesignerForm;
use App\Filament\Resources\InteriorDesigners\Schemas\InteriorDesignerInfolist;
use App\Filament\Resources\InteriorDesigners\Tables\InteriorDesignersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InteriorDesignerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $navigationLabel = 'Interior Designers';
    
    protected static ?string $modelLabel = 'Interior Designer';
    
    protected static ?string $pluralModelLabel = 'Interior Designers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'interior_designer');
    }

    public static function form(Schema $schema): Schema
    {
        return InteriorDesignerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InteriorDesignerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InteriorDesignersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteriorDesigners::route('/'),
            'create' => CreateInteriorDesigner::route('/create'),
            'view' => ViewInteriorDesigner::route('/{record}'),
            'edit' => EditInteriorDesigner::route('/{record}/edit'),
        ];
    }
}
