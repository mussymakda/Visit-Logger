<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Designer\DashboardController;
use App\Http\Controllers\ReportController;
use App\Models\Sponsor;

Route::get('/', function () {
    // Redirect to admin panel by default
    return redirect('/admin');
});

// Export routes for reports
Route::prefix('admin/reports')->middleware(['auth', 'verified'])->name('admin.reports.')->group(function () {
    Route::get('designer/export', [ReportController::class, 'exportDesignerReport'])->name('designer.export');
    Route::get('sponsor/export', [ReportController::class, 'exportSponsorReport'])->name('sponsor.export');
    Route::get('visits/export', [ReportController::class, 'exportAllVisitsReport'])->name('visits.export');
});

// QR Scanner for Interior Designers
Route::middleware(['auth'])->group(function () {
    // Simple QR Scanner page (no Filament/Livewire)
    Route::get('/scanner', function () {
        return view('scanner');
    })->middleware('auth:web');

    Route::get('/qr-scanner', [App\Http\Controllers\Designer\DashboardController::class, 'index'])
        ->name('qr.scanner');
});

// Test QR codes page (for development)
Route::get('/test-qr', function () {
    $sponsors = Sponsor::limit(5)->get();
    return view('test-qr', compact('sponsors'));
});

// Template download routes for Excel imports
Route::get('/download/sponsor-template', function () {
    $headers = [
        'name',
        'company_name',
        'contact',
        'location',
        'description'
    ];
    
    $callback = function() use ($headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $headers);
        
        // Add sample data
        fputcsv($file, [
            'Sample Sponsor',
            'Sample Company Ltd',
            'contact@example.com',
            'New York, NY',
            'Sample sponsor description'
        ]);
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="sponsor_import_template.csv"',
    ]);
})->name('download.sponsor-template');

Route::get('/download/designer-template', function () {
    $headers = [
        'name',
        'email',
        'password'
    ];
    
    $callback = function() use ($headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $headers);
        
        // Add sample data
        fputcsv($file, [
            'John Designer',
            'john@example.com',
            'password123'
        ]);
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="designer_import_template.csv"',
    ]);
})->name('download.designer-template');
