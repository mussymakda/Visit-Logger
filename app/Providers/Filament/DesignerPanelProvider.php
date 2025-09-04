<?php

namespace App\Providers\Filament;

use App\Models\Settings;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DesignerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Settings::getInstance();
        
        $panel = $panel
            ->id('designer')
            ->path('designer')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light);
            
        // Apply settings if available
        if ($settings) {
            if ($settings->app_name) {
                $panel->brandName($settings->app_name);
            }
            
            if ($settings->app_logo) {
                $panel->brandLogo(asset('storage/' . $settings->app_logo));
                $panel->brandLogoHeight('4rem');
            }
            
            if ($settings->favicon) {
                $panel->favicon(asset('storage/' . $settings->favicon));
            }
        }
        
        return $panel
            ->discoverResources(in: app_path('Filament/Designer/Resources'), for: 'App\Filament\Designer\Resources')
            ->discoverPages(in: app_path('Filament/Designer/Pages'), for: 'App\Filament\Designer\Pages')
            ->pages([
                \App\Filament\Designer\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Designer/Widgets'), for: 'App\Filament\Designer\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web');
    }
}
