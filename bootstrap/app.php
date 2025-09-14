<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configure authentication redirects for Filament
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            // Check if request is for admin panel
            if ($request->is('admin*')) {
                return route('filament.admin.auth.login');
            }
            
            // Check if request is for designer panel - redirect to custom login
            if ($request->is('designer*')) {
                return route('designer.login');
            }
            
            // For other routes, redirect to admin panel (default)
            return route('filament.admin.auth.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
