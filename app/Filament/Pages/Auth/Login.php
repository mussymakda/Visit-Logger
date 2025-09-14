<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Checkbox;
use Filament\Schemas\Components\Component;

class Login extends BaseLogin
{
    protected function getRememberFormComponent(): Component 
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'))
            ->default(true); // Default to checked
    }
}
