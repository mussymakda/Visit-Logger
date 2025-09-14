<?php

namespace App\Filament\Resources\Sponsors\Pages;

use App\Filament\Resources\Sponsors\SponsorResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Settings;

class ViewSponsor extends ViewRecord
{
    protected static string $resource = SponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPDF')
                ->label('Download PDF QR Code')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $settings = Settings::getInstance();
                    
                    $pdf = Pdf::loadView('pdf.sponsor-qr', [
                        'sponsor' => $this->record,
                        'settings' => $settings
                    ]);
                    
                    $filename = 'sponsor-qr-' . str_replace(' ', '-', strtolower($this->record->name)) . '.pdf';
                    
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename, [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
            EditAction::make(),
        ];
    }
}
