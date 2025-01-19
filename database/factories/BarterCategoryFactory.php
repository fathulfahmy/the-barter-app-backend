<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterCategory>
 */
class BarterCategoryFactory extends Factory
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
            'name' => fake()->words(2, true),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
