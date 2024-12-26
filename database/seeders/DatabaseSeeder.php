<?php

namespace Database\Seeders;

use App\Models\BarterCategory;
use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use GetStream\StreamChat\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->clearMedia();
        $this->seedUsers(10);
        $this->seedStreamChatUsers();
        $this->seedBarterCategories(10);
        $this->seedBarterServices(5);
        $this->seedBarterTransactions(count: 1000);

        $this->command->info('Seeding complete!');
    }

    protected function clearMedia()
    {
        $this->command->info('Clearing media...');

        if (Storage::disk('public')->exists('media')) {
            Storage::disk('public')->deleteDirectory('media');
            Storage::disk('public')->makeDirectory('media');
        }
    }

    protected function seedUsers(int $count): void
    {
        $this->command->info("Seeding $count users...");

        for ($i = 0; $i < $count; $i++) {
            if ($i === 0) {
                $user = User::factory()->create([
                    'name' => 'Demo User',
                    'email' => 'user@demo.com',
                ]);
            } else {
                $user = User::factory()->create();
            }
        }
    }

    protected function seedStreamChatUsers()
    {
        $this->command->info('Seeding stream chat users...');

        $users = User::all();
        foreach ($users as $user) {
            $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));
            $chat_client->upsertUsers([
                [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'role' => 'user',
                ],
            ]);
        }
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
}
