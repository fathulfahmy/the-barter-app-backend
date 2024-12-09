<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterTransaction>
 */
class BarterTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = [
            'pending',
            'accepted',
            'rejected',
            'completed',
            'cancelled',
        ];

        return [
            'barter_acquirer_id' => fake()->numberBetween(1, 10),
            'barter_provider_id' => fake()->numberBetween(1, 10),
            'barter_service_id' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement($status),
        ];
    }
}
