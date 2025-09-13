<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Get sponsor data by ID
     */
    public function getSponsor($id)
    {
        try {
            // Check if user is authenticated (check both guards)
            if (!Auth::check() && !Auth::guard('designer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }
            
            $sponsor = Sponsor::findOrFail($id);
            
            // Return direct sponsor object data without nesting
            return response()->json([
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'company_name' => $sponsor->company_name,
                'google_reviews_link' => $sponsor->google_reviews_link,
                'contact' => $sponsor->contact,
                'location' => $sponsor->location,
                'description' => $sponsor->description,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Sponsor with ID {$id} not found"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Submit visit log
     */
    public function submitVisit(Request $request)
    {
        try {
                        // Debug authentication status
            Log::info('API Visit Submission Debug', [
                'auth_check' => Auth::check(),
                'auth_id' => Auth::id(),
                'designer_auth_check' => Auth::guard('designer')->check(),
                'designer_auth_id' => Auth::guard('designer')->id(),
                'session_id' => session()->getId(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'headers' => $request->headers->all()
            ]);
            
            // Check if user is authenticated (check both guards)
            if (!Auth::check() && !Auth::guard('designer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'debug' => [
                        'session_id' => session()->getId(),
                        'auth_guard' => config('auth.defaults.guard'),
                        'designer_guard_check' => Auth::guard('designer')->check(),
                    ]
                ], 401);
            }
            
            $request->validate([
                'sponsor_id' => 'required|exists:sponsors,id',
                'site_photo' => 'required|file|mimes:jpeg,jpg,png|max:10240', // 10MB max
                'visited_at' => 'required|date',
                'notes' => 'nullable|string|max:1000', // Allow optional notes
            ]);
            
            // Store the uploaded photo
            $photoPath = $request->file('site_photo')->store('visit-photos', 'public');
            
            // Prioritize designer guard for API calls, fallback to web guard
            $userId = Auth::guard('designer')->id() ?: Auth::id();
            
            Log::info('API Visit User Selection', [
                'designer_guard_id' => Auth::guard('designer')->id(),
                'web_guard_id' => Auth::id(),
                'selected_user_id' => $userId,
            ]);
            
            // Create visit record
            $visit = Visit::create([
                'user_id' => $userId,
                'sponsor_id' => $request->sponsor_id,
                'photo' => $photoPath,
                'visited_at' => $request->visited_at,
                'notes' => $request->notes, // Save notes if provided
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Visit logged successfully',
                'visit' => $visit
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logging visit: ' . $e->getMessage()
            ], 500);
        }
    }
}
