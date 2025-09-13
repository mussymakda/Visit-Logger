<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateDesignerSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (! $request->hasSession() || ! $request->user('designer')) {
            return $next($request);
        }

        if (! $request->session()->has('password_hash_designer')) {
            $this->storePasswordHashInSession($request);
        }

        if ($request->session()->get('password_hash_designer') !== $request->user('designer')->getAuthPassword()) {
            $this->logout($request);
        }

        return tap($next($request), function () use ($request) {
            if (! is_null(Auth::guard('designer')->user())) {
                $this->storePasswordHashInSession($request);
            }
        });
    }

    /**
     * Store the user's current password hash in the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function storePasswordHashInSession($request)
    {
        if (! $request->user('designer')) {
            return;
        }

        $request->session()->put([
            'password_hash_designer' => $request->user('designer')->getAuthPassword(),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function logout($request)
    {
        Auth::guard('designer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
