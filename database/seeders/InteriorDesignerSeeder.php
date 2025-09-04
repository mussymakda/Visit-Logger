<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InteriorDesignerSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Jane Designer',
            'email' => 'designer@test.com',
            'password' => Hash::make('password'),
            'role' => 'interior_designer',
        ]);
    }
}
