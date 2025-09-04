<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Models\Sponsor;

// Public API endpoint for sponsor lookup (temporary for testing)
Route::get('/sponsors/{id}', function ($id) {
    try {
        $sponsor = Sponsor::findOrFail($id);
        
        // Return direct sponsor object data without nesting
        return response()->json([
            'id' => $sponsor->id,
            'name' => $sponsor->name,
            'company_name' => $sponsor->company_name,
            'contact' => $sponsor->contact,
            'location' => $sponsor->location,
            'description' => $sponsor->description,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Sponsor not found'
        ], 404);
    }
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API endpoints with web session support for Filament integration
Route::middleware(['web'])->group(function () {
    Route::post('/visits', [ApiController::class, 'submitVisit']);
});
