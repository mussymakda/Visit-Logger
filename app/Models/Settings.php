<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Settings extends Model
{
    protected $fillable = [
        'app_name',
        'app_logo',
        'favicon',
        'footer_text',
    ];

    public static function getInstance()
    {
        try {
            // Check if the settings table exists before querying
            if (!Schema::hasTable('settings')) {
                // Return a default instance with fallback values
                $instance = new static();
                $instance->app_name = config('app.name', 'Visit Logger');
                $instance->app_logo = null;
                $instance->favicon = null;
                $instance->footer_text = null;
                return $instance;
            }

            return static::firstOrCreate([]);
        } catch (\Exception $e) {
            // Log the error for debugging but don't break the application
            Log::debug('Settings model error during bootstrap: ' . $e->getMessage());
            
            // Return a default instance with fallback values
            $instance = new static();
            $instance->app_name = config('app.name', 'Visit Logger');
            $instance->app_logo = null;
            $instance->favicon = null;
            $instance->footer_text = null;
            return $instance;
        }
    }
}
