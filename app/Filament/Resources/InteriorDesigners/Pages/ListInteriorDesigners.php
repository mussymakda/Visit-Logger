<?php

namespace App\Filament\Resources\InteriorDesigners\Pages;

use App\Filament\Resources\InteriorDesigners\InteriorDesignerResource;
use App\Imports\InteriorDesignerImport;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListInteriorDesigners extends ListRecords
{
    protected static string $resource = InteriorDesignerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importDesigners')
                ->label('Import from Excel')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->helperText('Upload an Excel file with columns: name, email, password (optional - defaults to password123)')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    try {
                        $import = new InteriorDesignerImport();
                        Excel::import($import, $data['file']);
                        
                        Notification::make()
                            ->title('Interior Designers imported successfully!')
                            ->success()
                            ->send();
                            
                        $this->redirect(static::getUrl());
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url('/download/designer-template')
                ->openUrlInNewTab(),
            
            CreateAction::make(),
        ];
    }
}
