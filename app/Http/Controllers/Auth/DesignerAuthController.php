<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DesignerAuthController extends Controller
{
    /**
     * Show the designer registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.designer.register');
    }

    /**
     * Handle designer registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'interior_designer',
        ]);

        Auth::login($user);

        return redirect()->route('filament.designer.pages.dashboard');
    }

    /**
     * Show the designer login form.
     */
    public function showLoginForm()
    {
        return view('auth.designer.login');
    }

    /**
     * Handle designer login by name search.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Search for user by name or email
        $user = User::where('role', 'interior_designer')
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', $request->search);
            })
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'search' => ['No designer found with that name or email.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->intended(route('filament.designer.pages.dashboard'));
    }

    /**
     * Search for designers by name (AJAX endpoint).
     */
    public function searchDesigners(Request $request)
    {
        $search = $request->get('q', '');
        
        Log::info('Designer search called', ['query' => $search]);
        
        if (strlen($search) < 2) {
            Log::info('Search too short, returning empty');
            return response()->json([]);
        }

        $designers = User::where('role', 'interior_designer')
            ->where('name', 'like', '%' . $search . '%')
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        Log::info('Search results', ['count' => $designers->count(), 'results' => $designers->toArray()]);

        return response()->json($designers);
    }

    /**
     * Logout designer.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('designer.login');
    }
}
