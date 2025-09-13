<?php

namespace App\Imports;

use App\Models\Sponsor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SponsorImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $sponsor = new Sponsor([
            'name' => $row['name'],
            'company_name' => $row['company_name'] ?? null,
            'google_reviews_link' => $row['google_reviews_link'] ?? null,
            'contact' => $row['contact'] ?? null,
            'location' => $row['location'] ?? null,
            'description' => $row['description'] ?? null,
            'logo' => null, // Logo uploads handled separately via admin interface
        ]);
        
        // Generate QR code after creating sponsor
        $sponsor->save();
        $sponsor->generateQrCode();
        $sponsor->save();
        
        return $sponsor;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'google_reviews_link' => 'nullable|url|max:500',
            'contact' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
