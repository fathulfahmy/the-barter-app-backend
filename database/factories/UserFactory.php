<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $exploded = explode(' ', trim($name));
        $cleaned = array_map(function ($part) {
            return preg_replace('/[^a-zA-Z0-9]/', '', $part);
        }, $exploded);
        $imploded = implode('.', $cleaned);
        $email = strtolower($imploded).'@demo.com';

        $bank_name = fake()->randomElement([
            'Affin Bank',
            'Agro Bank',
            'Alliance Bank',
            'Ambank',
            'Bank Islam',
            'Bank Muamalat',
            'Bank Rakyat',
            'Bank Simpanan Malaysia',
            'CIMB Bank',
            'Hong Leong Bank',
            'HSBC Bank',
            'Maybank',
            'OCBC Bank',
            'Public Bank',
            'RHB Bank',
            'Standard Chartered Bank',
            'United Overseas Bank',
        ]);

        $date = fake()->dateTimeThisMonth();

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'bank_name' => $bank_name,
            'bank_account_number' => fake('ms_MY')->bankAccountNumber(),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        $date = fake()->dateTimeThisMonth();

        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
