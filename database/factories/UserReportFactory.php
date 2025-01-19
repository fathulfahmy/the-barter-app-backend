<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReport>
 */
class UserReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeThisMonth();

        $models = [
            'user',
            'barter_service',
        ];

        $status = [
            'unread',
            'read',
        ];

        return [
            'author_id' => fake()->numberBetween(1, 10),
            'user_report_reason_id' => fake()->numberBetween(1, 10),
            'model_id' => fake()->numberBetween(1, 10),
            'model_name' => fake()->randomElement($models),
            'status' => fake()->randomElement($status),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
