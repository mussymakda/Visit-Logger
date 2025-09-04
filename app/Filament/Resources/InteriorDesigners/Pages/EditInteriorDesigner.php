<?php

namespace App\Filament\Resources\InteriorDesigners\Pages;

use App\Filament\Resources\InteriorDesigners\InteriorDesignerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInteriorDesigner extends EditRecord
{
    protected static string $resource = InteriorDesignerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
