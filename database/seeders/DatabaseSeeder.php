<?php

namespace Database\Seeders;

use App\Models\BarterCategory;
use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
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
        $this->clearChat();
        $this->seedUsers(10);
        $this->seedBarterCategories(10);
        $this->seedBarterServices(5);
        $this->seedBarterServiceImages(5);
        $this->seedBarterTransactions(count: 1000);
    }

    protected function clearMedia()
    {
        if (Storage::disk('public')->exists('media')) {
            Storage::disk('public')->deleteDirectory('media');
            Storage::disk('public')->makeDirectory('media');
        }

        $this->command->info('Cleared media directory');
    }

    protected function clearChat()
    {
        $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));

        $filters = [
            'role' => ['$eq' => 'user'],
        ];
        $query = $chat_client->queryUsers($filters);

        if (count($query['users']) > 0) {
            $user_ids = array_map(function ($user) {
                return $user['id'];
            }, $query['users']);

            $chat_client->deleteUsers($user_ids, [
                'user' => 'hard',
                'messages' => 'hard',
                'conversations' => 'hard',
            ]);
        }

        // wait for stream chat api delete to complete to prevent conflict with upsertion
        sleep(5);

        $this->command->info('Cleared stream chat users, messages and conversations!');
    }

    protected function seedUsers(int $count): void
    {
        $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));

        for ($i = 0; $i < $count; $i++) {
            if ($i === 0) {
                $user = User::factory()->create([
                    'name' => 'Demo User 1',
                    'email' => 'user1@demo.com',
                ]);
            } elseif ($i === 1) {
                $user = User::factory()->create([
                    'name' => 'Demo User 2',
                    'email' => 'user2@demo.com',
                ]);
            } else {
                $user = User::factory()->create();
            }

            $chat_client->upsertUsers([
                [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'role' => 'user',
                    'image' => $user->avatar['uri'],
                ],
            ]);
        }

        $this->command->info("Seeded $count users");
    }

    protected function seedBarterCategories(int $count): void
    {
        BarterCategory::factory($count)->create();

        $this->command->info("Seeded $count categories");
    }

    protected function seedBarterServices(int $service_per_user): void
    {
        $this->command->info("Seeded $service_per_user services per user");

        User::all()->each(function (User $user) use ($service_per_user) {
            BarterService::factory($service_per_user)->create([
                'barter_provider_id' => $user->id,
            ]);
        });
    }

    protected function seedBarterServiceImages(int $image_per_service)
    {
        BarterService::all()->each(function (BarterService $barter_service) use ($image_per_service) {
            for ($i = 0; $i < $image_per_service; $i++) {
                $barter_service
                    ->addMedia(config('app.default.image.path'))
                    ->preservingOriginal()
                    ->toMediaCollection('barter_service_images');
            }
        });

        $this->command->info("Seeded $image_per_service images per service");
    }

    protected function seedBarterTransactions(int $count): void
    {
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

                $random_id = fake()->randomElement([$barter_transaction->barter_acquirer_id, $barter_transaction->barter_provider_id]);

                if ($barter_transaction->status === 'awaiting_completed') {
                    $barter_transaction->update(['awaiting_completed_user_id' => $random_id]);
                }

                if ($barter_transaction->status === 'completed') {
                    if (rand(0, 1)) {
                        BarterReview::factory()->create([
                            'author_id' => $barter_transaction->barter_acquirer_id,
                            'barter_service_id' => $barter_transaction->barter_service_id,
                            'barter_transaction_id' => $barter_transaction->id,
                        ]);
                    }

                    if (rand(0, 1)) {
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

        $this->command->info("Seeded $count transactions");
    }
}
