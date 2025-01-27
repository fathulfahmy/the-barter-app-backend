<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterRemark>
 */
class BarterRemarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $address = fake()->randomDigit().', '.fake('ms_MY')->township().', '.fake('ms_MY')->townState();
        $date = fake()->dateTimeThisMonth();

        return [
            'datetime' => rand(0, 1) ? fake()->dateTimeThisMonth() : null,
            'address' => rand(0, 1) ? $address : null,
            'deliverables' => rand(0, 1) ? fake()->sentences(3) : null,
            'note' => rand(0, 1) ? fake()->sentence() : null,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
