<?php

namespace Database\Seeders;

use App\Models\BarterCategory;
use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@test.com',
        ]);

        User::factory(9)->create();

        $categories = [
            'Category 1',
            'Category 2',
            'Category 3',
            'Category 4',
            'Category 5',
            'Category 6',
            'Category 7',
            'Category 8',
            'Category 9',
            'Category 10',
        ];
        foreach ($categories as $category) {
            BarterCategory::create(['name' => $category]);
        }

        for ($i = 1; $i <= 50; $i++) {
            $amount = fake()->randomFloat(2, 0, 99);
            $status = fake()->randomElement(['pending', 'success', 'failed']);

            BarterService::factory()->create();
            BarterTransaction::factory()->create([
                'barter_service_id' => $i,
                'amount' => $amount,
                'status' => $status,
            ]);
        }

        for ($i = 1; $i <= 50; $i++) {
            $barter_service_ids = [
                fake()->numberBetween(1, 25),
                fake()->numberBetween(26, 50),
            ];
            $barter_invoice = BarterInvoice::factory()->create([
                'barter_transaction_id' => $i,
                'amount' => $amount,
                'status' => $status,
            ]);
            $barter_invoice->barter_services()->attach($barter_service_ids);

            BarterReview::factory()->create([
                'barter_service_id' => $i,
                'barter_transaction_id' => $i,
            ]);
        }
    }
}
