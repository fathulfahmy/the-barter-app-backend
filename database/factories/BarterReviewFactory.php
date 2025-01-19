<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterReview>
 */
class BarterReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeThisMonth();

        return [
            'author_id' => fake()->numberBetween(1, 10),
            'barter_service_id' => fake()->numberBetween(1, 50),
            'barter_transaction_id' => fake()->numberBetween(1, 50),
            'description' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 5),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
