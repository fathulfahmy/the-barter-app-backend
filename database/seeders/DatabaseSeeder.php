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
use Illuminate\Support\Facades\Storage;

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
        $this->seedBarterTransactions(count: 1000);
        $this->clearMedia();

        $this->command->info('Seeding complete!');
    }

    protected function seedUsers(int $count): void
    {
        $this->command->info("Seeding $count users...");

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@demo.com',
        ]);
        User::factory($count - 1)->create();
    }

    protected function seedBarterCategories(int $count): void
    {
        $this->command->info("Seeding $count categories...");

        BarterCategory::factory($count)->create();
    }

    protected function seedBarterServices(int $count_per_user): void
    {
        $this->command->info("Seeding $count_per_user services per user...");

        User::all()->each(function (User $user) use ($count_per_user) {
            BarterService::factory($count_per_user)->create([
                'barter_provider_id' => $user->id,
            ]);
        });
    }

    protected function seedBarterTransactions(int $count): void
    {
        $this->command->info("Seeding $count transactions...");

        for ($i = 0; $i < $count; $i++) {
            $user = User::inRandomOrder()->first();
            $barter_service = BarterService::whereNot('barter_provider_id', $user->id)
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
                        'author_id' => $barter_transaction->barter_acquirer_id,
                        'barter_service_id' => $barter_transaction->barter_service_id,
                        'barter_transaction_id' => $barter_transaction->id,
                    ]);

                    foreach ($barter_invoice->barter_services as $barter_service) {
                        BarterReview::factory()->create([
                            'author_id' => $barter_transaction->barter_provider_id,
                            'barter_service_id' => $barter_service->id,
                            'barter_transaction_id' => $barter_transaction->id,
                        ]);
                    }
                }
            }
        }
    }

    protected function clearMedia()
    {
        $this->command->info('Clearing media...');

        if (Storage::disk('public')->exists('media')) {
            Storage::disk('public')->deleteDirectory('media');
            Storage::disk('public')->makeDirectory('media');
        }
    }
}
