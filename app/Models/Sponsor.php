<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'contact',
        'location',
        'description',
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
        $qrData = (string) $this->id;
        $this->qr_code = $qrData;

        // For now, we'll just store the QR data and generate image URL via online service
        // This avoids the GD extension requirement
        $this->qr_code_path = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
        $this->saveQuietly(); // Use saveQuietly to avoid infinite loop
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }
}
