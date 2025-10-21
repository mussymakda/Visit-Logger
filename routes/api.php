<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DesignerApiController;
use App\Models\Sponsor;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =============================================================================
// DESIGNER MOBILE APP API
// =============================================================================

// Designer API routes with Sanctum authentication
Route::prefix('designer')->group(function () {
    Route::post('/login', [DesignerApiController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [DesignerApiController::class, 'logout']);
        Route::post('/verify-qr', [DesignerApiController::class, 'verifyQR']);
        Route::post('/submit-visit', [DesignerApiController::class, 'submitVisit']);
        Route::get('/get-visits', [DesignerApiController::class, 'getVisits']);
        Route::get('/get-stats', [DesignerApiController::class, 'getStats']);
        Route::get('/search-visits', [DesignerApiController::class, 'searchVisits']);
    });
});

// =============================================================================
// LEGACY/TESTING ENDPOINTS
// =============================================================================

// Public API endpoint for sponsor lookup (for testing purposes)
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
            'google_reviews_link' => $sponsor->google_reviews_link,
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
