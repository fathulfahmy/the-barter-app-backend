<?php

namespace Database\Seeders;

use App\Models\BarterCategory;
use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
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
        $this->seedUsers(10);
        $this->seedBarterCategories(10);
        $this->seedBarterServices(5);
        $this->seedBarterServiceImages(5);
        $this->seedBarterTransactions(count: 1000);
        $this->seedMessages(2);

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

    public function seedMessages($count_per_user)
    {
        $this->command->info("Seeding $count_per_user messages per user...");

        $users = User::all();

        $user = $users->first();
        $other_users = $users->except($user->id);

        foreach ($other_users as $other_user) {
            $chat_conversation = ChatConversation::factory()->create();
            $chat_conversation->users()->attach([$user->id, $other_user->id]);

            ChatMessage::factory($count_per_user)->create([
                'chat_conversation_id' => $chat_conversation->id,
                'author_id' => $user->id,
            ]);

            ChatMessage::factory($count_per_user)->create([
                'chat_conversation_id' => $chat_conversation->id,
                'author_id' => $other_user->id,
            ]);
        }
    }
}
