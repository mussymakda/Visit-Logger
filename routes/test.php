<?php

use Illuminate\Support\Facades\Route;
use App\Models\Sponsor;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/test-pdf/{sponsor}', function (Sponsor $sponsor) {
    $settings = Settings::getInstance();
    
    $pdf = Pdf::loadView('pdf.sponsor-qr', [
        'sponsor' => $sponsor,
        'settings' => $settings
    ]);
    
    return $pdf->stream('test-sponsor-qr.pdf');
});
