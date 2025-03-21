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
        $status = fake()->randomElement(['enabled', 'disabled']);
        $date = fake()->dateTimeThisMonth();

        return [
            'barter_provider_id' => fake()->numberBetween(1, 10),
            'barter_category_id' => fake()->numberBetween(1, 10),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'min_price' => $min_price,
            'max_price' => $max_price,
            'price_unit' => fake()->word(),
            'status' => $status,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
