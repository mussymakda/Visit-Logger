<?php

namespace App\Filament\Resources\Sponsors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Settings;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                    
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->size(40)
                    ->placeholder('No logo')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('google_reviews_link')
                    ->label('Google Reviews')
                    ->url(fn ($record) => $record->google_reviews_link)
                    ->openUrlInNewTab()
                    ->placeholder('Not provided')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('contact')
                    ->searchable(),
                    
                TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                    
                ImageColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->disk('local')
                    ->visibility('public')
                    ->size(60),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('downloadQRPDF')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function ($record) {
                        $settings = Settings::getInstance();
                        
                        $pdf = Pdf::loadView('pdf.sponsor-qr', [
                            'sponsor' => $record,
                            'settings' => $settings
                        ]);
                        
                        $filename = 'sponsor-qr-' . str_replace(' ', '-', strtolower($record->name)) . '.pdf';
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
                    
                Action::make('downloadQR')
                    ->label('QR Image')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('sponsors.download-qr', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->qr_code_path !== null),
                    
                Action::make('viewQR')
                    ->label('View QR')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->qr_code_path)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->qr_code_path !== null),
                    
                Action::make('directLink')
                    ->label('Direct Link')
                    ->icon('heroicon-o-link')
                    ->url(fn ($record) => url("/designer?sponsor={$record->id}"))
                    ->openUrlInNewTab()
                    ->color('success'),
                    
                Action::make('regenerateQR')
                    ->label('Regenerate QR')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn ($record) => $record->generateQrCode())
                    ->requiresConfirmation()
                    ->modalDescription('This will generate a new QR code for this sponsor.'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    Action::make('downloadMultiplePDFs')
                        ->label('Download Selected PDFs')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action(function ($records) {
                            $settings = Settings::getInstance();
                            $zip = new \ZipArchive();
                            $tempZipFile = tempnam(sys_get_temp_dir(), 'sponsor_qr_pdfs');
                            
                            if ($zip->open($tempZipFile, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $record) {
                                    $pdf = Pdf::loadView('pdf.sponsor-qr', [
                                        'sponsor' => $record,
                                        'settings' => $settings
                                    ]);
                                    
                                    $filename = 'sponsor-qr-' . str_replace(' ', '-', strtolower($record->name)) . '.pdf';
                                    $zip->addFromString($filename, $pdf->output());
                                }
                                $zip->close();
                                
                                return response()->download($tempZipFile, 'sponsor-qr-codes.zip')->deleteFileAfterSend();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalDescription('This will generate and download a ZIP file containing PDF QR codes for all selected sponsors.'),
                ]),
            ]);
    }
}
