<?php

use App\Models\Sponsor;
use Illuminate\Support\Facades\Route;

Route::get('/test-qr-download', function () {
    $sponsor = Sponsor::first();
    
    if (!$sponsor || !$sponsor->qr_code_path) {
        return 'No sponsor with QR code found';
    }
    
    echo "Testing QR download for: " . $sponsor->name . "<br>";
    echo "QR Code Path: " . $sponsor->qr_code_path . "<br>";
    
    // Test if we can fetch the QR image
    $imageContent = file_get_contents($sponsor->qr_code_path);
    
    if ($imageContent === false) {
        return 'Failed to fetch QR image from external service';
    }
    
    echo "QR Image fetched successfully! Size: " . strlen($imageContent) . " bytes<br>";
    echo '<img src="' . $sponsor->qr_code_path . '" alt="QR Code" style="max-width: 200px;"><br>';
    echo '<a href="' . $sponsor->qr_code_path . '" download="qr-test.png">Download QR</a>';
    
    return '';
});