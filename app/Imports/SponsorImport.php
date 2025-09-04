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
            'contact' => $row['contact'] ?? null,
            'location' => $row['location'] ?? null,
            'description' => $row['description'] ?? null,
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
            'contact' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
