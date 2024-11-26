<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterService>
 */
class BarterServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $min_price = fake()->randomFloat(2, 0, 99);
        $max_price = fake()->randomFloat(2, $min_price, 99);
        $status = [
            'active',
            'inactive',
        ];

        return [
            'barter_provider_id' => fake()->numberBetween(1, 10),
            'barter_category_id' => fake()->numberBetween(1, 10),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => 'RM' . $min_price . ' to ' . 'RM' . $max_price,
            'rating' => fake()->randomFloat(1, 1, 5),
            'status' => fake()->randomElement($status),
        ];
    }
}
