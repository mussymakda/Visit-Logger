<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'John Smith',
                'company_name' => 'ABC Construction',
                'contact' => 'john@abcconstruction.com',
                'location' => '123 Main St, Downtown',
                'description' => 'Leading construction company specializing in commercial buildings.',
            ],
            [
                'name' => 'Sarah Johnson',
                'company_name' => 'Johnson Architects',
                'contact' => 'sarah@johnsonarch.com',
                'location' => '456 Design Ave, Creative District',
                'description' => 'Award-winning architectural firm with 20+ years experience.',
            ],
            [
                'name' => 'Mike Wilson',
                'company_name' => 'Wilson Interiors',
                'contact' => 'mike@wilsoninteriors.com',
                'location' => '789 Style Blvd, Arts Quarter',
                'description' => 'Premium interior design services for luxury projects.',
            ],
        ];

        foreach ($sponsors as $sponsorData) {
            Sponsor::create($sponsorData);
        }
    }
}
