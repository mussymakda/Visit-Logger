<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'google_reviews_link',
        'contact',
        'location',
        'description',
        'logo',
        'qr_code',
        'qr_code_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($sponsor) {
            $sponsor->generateQrCode();
        });

        static::updated(function ($sponsor) {
            if ($sponsor->wasChanged(['name', 'company_name'])) {
                $sponsor->generateQrCode();
            }
        });
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function generateQrCode()
    {
        // Generate a direct link to the designer panel with sponsor parameter
        $designerUrl = url("/designer?sponsor={$this->id}");
        $this->qr_code = $designerUrl;

        // Generate QR code image URL via online service
        $this->qr_code_path = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($designerUrl);
        $this->saveQuietly(); // Use saveQuietly to avoid infinite loop
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path;
    }
}
