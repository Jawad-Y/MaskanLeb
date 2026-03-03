<?php

namespace Database\Factories;

use App\Models\Judiciary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),
            'judiciary_id' => Judiciary::inRandomOrder()->first()?->id ?? 1,
            'title' => fake()->randomElement([
                'Modern', 'Cozy', 'Spacious', 'Luxury', 'Charming', 'Beautiful',
            ]).' '.fake()->numberBetween(1, 5).' Bedroom Apartment in '.fake()->city(),
            'description' => fake()->paragraphs(3, true),
            'price_usd' => fake()->randomFloat(2, 200, 5000),
            'number_of_rooms' => fake()->numberBetween(1, 6),
            'number_of_bathrooms' => fake()->numberBetween(1, 3),
            'size_m2' => fake()->numberBetween(40, 400),
            'furnished' => fake()->boolean(40),
            'parking' => fake()->boolean(50),
            'minimum_months' => fake()->randomElement([1, 3, 6, 12]),
            'latitude' => fake()->latitude(33.6, 34.5),
            'longitude' => fake()->longitude(35.2, 36.4),
            'status' => 'available',
            'is_verified' => fake()->boolean(30),
            'views_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => ['status' => 'available']);
    }

    public function rented(): static
    {
        return $this->state(fn () => ['status' => 'rented']);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
