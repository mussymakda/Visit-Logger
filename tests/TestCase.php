<?php

namespace Tests;

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default settings after database refresh
        $this->seed(\Database\Seeders\SettingsSeeder::class);
    }

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ]);
    }

    /**
     * Create an interior designer user for testing
     */
    protected function createDesignerUser(): User
    {
        return User::factory()->create([
            'role' => 'interior_designer',
            'email' => 'designer@test.com',
            'name' => 'Test Designer',
        ]);
    }

    /**
     * Create a test sponsor
     */
    protected function createSponsor(array $attributes = []): Sponsor
    {
        return Sponsor::factory()->create($attributes);
    }

    /**
     * Create a test visit
     */
    protected function createVisit(User $user, Sponsor $sponsor, array $attributes = []): Visit
    {
        return Visit::factory()->create(array_merge([
            'user_id' => $user->id,
            'sponsor_id' => $sponsor->id,
        ], $attributes));
    }
}
