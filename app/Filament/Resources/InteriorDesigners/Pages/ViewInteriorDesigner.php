<?php

namespace App\Filament\Resources\InteriorDesigners\Pages;

use App\Filament\Resources\InteriorDesigners\InteriorDesignerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInteriorDesigner extends ViewRecord
{
    protected static string $resource = InteriorDesignerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
