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
        $date = fake()->dateTimeThisMonth();

        $min_price = fake()->randomFloat(2, 0, 99);
        $max_price = fake()->randomFloat(2, $min_price, 99);
        $status = [
            'enabled',
            'disabled',
        ];

        return [
            'barter_provider_id' => fake()->numberBetween(1, 10),
            'barter_category_id' => fake()->numberBetween(1, 10),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'min_price' => $min_price,
            'max_price' => $max_price,
            'price_unit' => fake()->word(),
            'status' => fake()->randomElement($status),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
