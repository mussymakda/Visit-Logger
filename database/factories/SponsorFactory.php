<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sponsor>
 */
class SponsorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company_name' => $this->faker->company(),
            'contact' => $this->faker->phoneNumber(),
            'location' => $this->faker->address(),
            'description' => $this->faker->paragraph(),
            'google_reviews_link' => $this->faker->optional(0.3)->url(),
            'logo' => null, // Logo is optional and typically uploaded manually
        ];
    }
}
