<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing admin users to avoid duplicates
        User::where('email', 'admin@admin.com')->delete();
        
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
        $this->command->info("Admin user created:");
        $this->command->info("Email: {$admin->email}");
        $this->command->info("Password: admin123");
        $this->command->info("Role: {$admin->role}");
    }
}
