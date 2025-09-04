<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return static::firstOrCreate([]);
    }
}
