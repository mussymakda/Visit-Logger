<?php

namespace App\Filament\Designer\Pages;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        // Redirect to custom login page
        redirect('/auth/designer/login')->send();
    }
}
