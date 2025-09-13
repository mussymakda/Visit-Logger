<?php

namespace Database\Factories;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visitedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'user_id' => User::factory(),
            'sponsor_id' => Sponsor::factory(),
            'photo' => fake()->imageUrl(640, 480, 'business', true),
            'notes' => fake()->optional(0.6)->realText(200),
            'visit_location' => fake()->optional(0.7)->address(),
            'visited_at' => $visitedAt,
            'created_at' => $visitedAt,
            'updated_at' => $visitedAt,
        ];
    }

    /**
     * Indicate that the visit has a photo.
     */
    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo' => 'visit-photos/'.fake()->uuid().'.jpg',
        ]);
    }

    /**
     * Indicate that the visit does not have a photo.
     */
    public function withoutPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo' => '',
        ]);
    }

    /**
     * Indicate that the visit belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the visit belongs to a specific sponsor.
     */
    public function forSponsor(Sponsor $sponsor): static
    {
        return $this->state(fn (array $attributes) => [
            'sponsor_id' => $sponsor->id,
        ]);
    }
}
