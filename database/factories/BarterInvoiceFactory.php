<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterInvoice>
 */
class BarterInvoiceFactory extends Factory
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
            'barter_acquirer_id' => fake()->numberBetween(1, 10),
            'barter_transaction_id' => fake()->numberBetween(1, 50),
            'amount' => fake()->randomFloat(2, 1, 99),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
