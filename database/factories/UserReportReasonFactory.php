<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReportReason>
 */
class UserReportReasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = now()->startOfYear();

        return [
            'name' => fake()->sentence(),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
