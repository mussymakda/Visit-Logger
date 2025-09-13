<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Sponsor;
use Barryvdh\DomPDF\Facade\Pdf;

class SponsorPdfController extends Controller
{
    public function generateQrPdf(Sponsor $sponsor)
    {
        $settings = Settings::getInstance();

        $pdf = Pdf::loadView('pdf.sponsor-qr', [
            'sponsor' => $sponsor,
            'qrCodeUrl' => $sponsor->qr_code,
            'settings' => $settings,
        ]);

        $pdf->setPaper([0, 0, 283.46, 442.20], 'portrait'); // 100x156mm in points

        $filename = 'sponsor-qr-'.$sponsor->id.'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
