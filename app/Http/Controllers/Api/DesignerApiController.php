<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Sponsor;
use App\Models\Visit;
use Carbon\Carbon;

class DesignerApiController extends Controller
{
    /**
     * Designer Authentication
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        // Find user and check if they're an interior designer
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->role !== 'interior_designer') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Designer account required.'
            ], 403);
        }

        // Login the user using the designer guard
        Auth::guard('designer')->login($user);
        
        // Generate API token (using Laravel Sanctum)
        $token = $user->createToken('designer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Get authenticated designer profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->toISOString(),
                ]
            ]
        ]);
    }

    /**
     * Logout designer
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        Auth::guard('designer')->logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Verify QR Code and get sponsor information
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $qrData = $request->input('qr_data');
            $sponsorId = $this->extractSponsorId($qrData);

            if (!$sponsorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }

            $sponsor = Sponsor::find($sponsorId);
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'QR code verified successfully',
                'data' => [
                    'sponsor' => [
                        'id' => $sponsor->id,
                        'name' => $sponsor->name,
                        'company_name' => $sponsor->company_name,
                        'contact' => $sponsor->contact,
                        'location' => $sponsor->location,
                        'description' => $sponsor->description,
                        'google_reviews_link' => $sponsor->google_reviews_link,
                        'qr_code' => $sponsor->qr_code,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('QR verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify QR code'
            ], 500);
        }
    }

    /**
     * Submit a new visit log
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sponsor_id' => 'required|exists:sponsors,id',
            'notes' => 'required|string|max:1000',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Max 10MB
            'visit_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $sponsor = Sponsor::findOrFail($request->input('sponsor_id'));
            
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $user->id . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('visit-photos', $filename, 'public');
            }

            // Create visit record
            $visit = Visit::create([
                'user_id' => $user->id,
                'sponsor_id' => $sponsor->id,
                'notes' => $request->input('notes'),
                'photo' => $photoPath,
                'visited_at' => $request->input('visit_date') ? 
                    Carbon::parse($request->input('visit_date')) : 
                    now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visit logged successfully',
                'data' => [
                    'visit' => [
                        'id' => $visit->id,
                        'sponsor_name' => $sponsor->name,
                        'notes' => $visit->notes,
                        'photo_url' => $photoPath ? Storage::url($photoPath) : null,
                        'visit_date' => $visit->visited_at->toISOString(),
                        'created_at' => $visit->created_at->toISOString(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Visit submission error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to log visit'
            ], 500);
        }
    }

    /**
     * Get visit history for the authenticated designer
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisits(Request $request)
    {
        try {
            $user = $request->user();
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            
            $visits = Visit::with('sponsor')
                ->where('user_id', $user->id)
                ->orderBy('visited_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $visitsData = $visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'sponsor' => [
                        'id' => $visit->sponsor->id,
                        'name' => $visit->sponsor->name,
                        'company_name' => $visit->sponsor->company_name,
                        'location' => $visit->sponsor->location,
                    ],
                    'notes' => $visit->notes,
                    'photo_url' => $visit->photo ? Storage::url($visit->photo) : null,
                    'visit_date' => $visit->visited_at->toISOString(),
                    'created_at' => $visit->created_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'visits' => $visitsData,
                    'pagination' => [
                        'current_page' => $visits->currentPage(),
                        'last_page' => $visits->lastPage(),
                        'per_page' => $visits->perPage(),
                        'total' => $visits->total(),
                        'has_more_pages' => $visits->hasMorePages(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get visits error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve visits'
            ], 500);
        }
    }

    /**
     * Get visit statistics for the authenticated designer
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        try {
            $user = $request->user();
            $today = now()->startOfDay();
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();

            $stats = [
                'visits_today' => Visit::where('user_id', $user->id)
                    ->whereDate('visited_at', $today)
                    ->count(),
                
                'visits_this_week' => Visit::where('user_id', $user->id)
                    ->where('visited_at', '>=', $thisWeek)
                    ->count(),
                
                'visits_this_month' => Visit::where('user_id', $user->id)
                    ->where('visited_at', '>=', $thisMonth)
                    ->count(),
                
                'total_visits' => Visit::where('user_id', $user->id)->count(),
                
                'unique_sponsors' => Visit::where('user_id', $user->id)
                    ->distinct('sponsor_id')
                    ->count('sponsor_id'),
            ];

            // Recent visits for quick access
            $recentVisits = Visit::with('sponsor')
                ->where('user_id', $user->id)
                ->orderBy('visited_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'sponsor_name' => $visit->sponsor->name,
                        'visit_date' => $visit->visited_at->toISOString(),
                        'notes' => substr($visit->notes, 0, 100) . (strlen($visit->notes) > 100 ? '...' : ''),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_visits' => $recentVisits,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get stats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }

    /**
     * Search visits by sponsor name or notes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchVisits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $query = $request->input('query');
            
            $visits = Visit::with('sponsor')
                ->where('user_id', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('notes', 'like', '%' . $query . '%')
                      ->orWhereHas('sponsor', function ($sq) use ($query) {
                          $sq->where('name', 'like', '%' . $query . '%')
                            ->orWhere('company_name', 'like', '%' . $query . '%')
                            ->orWhere('location', 'like', '%' . $query . '%');
                      });
                })
                ->orderBy('visit_date', 'desc')
                ->limit(50)
                ->get();

            $visitsData = $visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'sponsor' => [
                        'id' => $visit->sponsor->id,
                        'name' => $visit->sponsor->name,
                        'company_name' => $visit->sponsor->company_name,
                        'location' => $visit->sponsor->location,
                    ],
                    'notes' => $visit->notes,
                    'photo_url' => $visit->photo ? Storage::url($visit->photo) : null,
                    'visit_date' => $visit->visited_at->toISOString(),
                    'created_at' => $visit->created_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'visits' => $visitsData,
                    'query' => $query,
                    'total_results' => $visits->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Search visits error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search visits'
            ], 500);
        }
    }

    /**
     * Get detailed information about a specific visit
     * 
     * @param Request $request
     * @param int $visitId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisit(Request $request, $visitId)
    {
        try {
            $user = $request->user();
            
            $visit = Visit::with('sponsor')
                ->where('user_id', $user->id)
                ->findOrFail($visitId);

            return response()->json([
                'success' => true,
                'data' => [
                    'visit' => [
                        'id' => $visit->id,
                        'sponsor' => [
                            'id' => $visit->sponsor->id,
                            'name' => $visit->sponsor->name,
                            'company_name' => $visit->sponsor->company_name,
                            'contact' => $visit->sponsor->contact,
                            'location' => $visit->sponsor->location,
                            'description' => $visit->sponsor->description,
                            'google_reviews_link' => $visit->sponsor->google_reviews_link,
                        ],
                        'notes' => $visit->notes,
                        'photo_url' => $visit->photo ? Storage::url($visit->photo) : null,
                        'visit_date' => $visit->visited_at->toISOString(),
                        'created_at' => $visit->created_at->toISOString(),
                        'updated_at' => $visit->updated_at->toISOString(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get visit error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Visit not found'
            ], 404);
        }
    }

    /**
     * Extract sponsor ID from various QR code formats
     * 
     * @param string $qrData
     * @return int|null
     */
    private function extractSponsorId($qrData)
    {
        // Handle different QR code formats:
        // Format 1: "sponsor=123"
        if (preg_match('/sponsor=(\d+)/', $qrData, $matches)) {
            return (int) $matches[1];
        }
        
        // Format 2: "SPONSOR-123"
        if (preg_match('/SPONSOR-(\d+)/', $qrData, $matches)) {
            return (int) $matches[1];
        }
        
        // Format 3: Plain number "123"
        if (is_numeric($qrData)) {
            return (int) $qrData;
        }
        
        // Format 4: URL with sponsor ID
        if (preg_match('/sponsor[\/=](\d+)/', $qrData, $matches)) {
            return (int) $matches[1];
        }
        
        return null;
    }
}
