<?php

use App\Models\Settings;
use App\Models\Sponsor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/test-pdf/{sponsor}', function (Sponsor $sponsor) {
    $settings = Settings::getInstance();

    $pdf = Pdf::loadView('pdf.sponsor-qr', [
        'sponsor' => $sponsor,
        'settings' => $settings,
    ]);

    return $pdf->stream('test-sponsor-qr.pdf');
});
