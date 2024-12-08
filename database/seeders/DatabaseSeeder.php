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
        $this->seedUsers(10);
        $this->seedBarterCategories(10);
        $this->seedBarterServices(5);
        $this->seedBarterTransactions(5000);
    }

    protected function seedUsers(int $count): void
    {
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@demo.com',
        ]);
        User::factory($count - 1)->create();
    }

    protected function seedBarterCategories(int $count): void
    {
        BarterCategory::factory($count)->create();
    }

    protected function seedBarterServices(int $count_per_user): void
    {
        User::all()->each(function (User $user) use ($count_per_user) {
            BarterService::factory($count_per_user)->create([
                'barter_provider_id' => $user->id,
            ]);
        });
    }

    protected function seedBarterTransactions(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $user = User::inRandomOrder()->first();
            $barter_service = BarterService::whereNotIn('barter_provider_id', $user->barter_services->pluck('id'))
                ->inRandomOrder()
                ->first();

            if ($barter_service) {
                $barter_transaction = BarterTransaction::factory()->create([
                    'barter_acquirer_id' => $user->id,
                    'barter_provider_id' => $barter_service->barter_provider_id,
                    'barter_service_id' => $barter_service->id,
                ]);

                $barter_invoice = BarterInvoice::factory()->create([
                    'barter_acquirer_id' => $user->id,
                    'barter_transaction_id' => $barter_transaction->id,
                ]);

                $barter_invoice->barter_services()->attach(
                    $user->barter_services->pluck('id')->random(2)
                );

                if ($barter_transaction->status === 'completed') {
                    BarterReview::factory()->create([
                        'author_id' => $user->id,
                        'barter_service_id' => $barter_transaction->barter_service_id,
                        'barter_transaction_id' => $barter_transaction->id,
                    ]);
                }
            }
        }
    }
}
