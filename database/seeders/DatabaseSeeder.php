<?php

namespace Database\Seeders;

use App\Models\BarterCategory;
use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
use App\Models\UserReport;
use App\Models\UserReportReason;
use GetStream\StreamChat\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->clearMedia();
        $this->clearChat();
        $this->seedBarterCategories();
        $this->seedUserReportReasons();
        $this->seedAdmins(2);
        $this->seedUsers(10);
        $this->seedBarterServices(5);
        $this->seedBarterTransactions(1000);
        $this->seedBarterReviews();
        $this->seedUserReports(10);
    }

    protected function clearMedia()
    {
        $directory = public_path('media');

        if (File::exists($directory)) {
            File::cleanDirectory($directory);
            $this->command->info('Cleared media');
        } else {
            $this->command->warn("Directory does not exist: $directory");
        }
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

        $this->command->info('Cleared chat');
    }

    protected function seedAdmins(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $id = $i + 1;
            User::factory()->create([
                'name' => "Admin $id",
                'email' => "admin$id@demo.com",
                'role' => 'admin',
                'bank_name' => null,
                'bank_account_number' => null,
            ]);
        }

        $this->command->info("Seeded $count admins");
    }

    protected function seedUsers(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $gender = fake()->randomElement(['male', 'female']);

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
                $name = fake()->name($gender);

                $exploded = explode(' ', trim($name));
                $cleaned = array_map(function ($part) {
                    return preg_replace('/[^a-zA-Z0-9]/', '', $part);
                }, $exploded);
                $imploded = implode('.', $cleaned);
                $email = strtolower($imploded).'@demo.com';

                $user = User::factory()->create([
                    'name' => $name,
                    'email' => $email,
                ]);
            }

            $this->addUserAvatar($user, $gender);
            $this->addChatUser($user);
        }

        $this->command->info("Seeded $count users");
    }

    protected function seedBarterCategories(): void
    {
        $dataset = require database_path('dataset/barter_services.php');

        $barter_categories = array_keys($dataset);

        foreach ($barter_categories as $category) {
            BarterCategory::factory()->create([
                'name' => $category,
            ]);
        }

        $this->command->info('Seeded categories');
    }

    protected function seedUserReportReasons(): void
    {
        $reasons = require database_path('dataset/user_report_reasons.php');

        foreach ($reasons as $reason) {
            UserReportReason::factory()->create([
                'name' => $reason,
            ]);
        }

        $this->command->info('Seeded reasons');
    }

    protected function seedBarterServices(int $service_per_user): void
    {
        $dataset = require database_path('dataset/barter_services.php');
        $barter_category_count = count($dataset);

        User::isNotAdmin()->each(function (User $user, $index) use ($dataset, $barter_category_count) {
            $barter_category_id = $index % $barter_category_count + 1;
            $barter_category = BarterCategory::where('id', $barter_category_id)->first();

            foreach ($dataset[$barter_category->name] as $data) {
                $title = $data['title'] ?? 'Default Service';
                $description = $data['description'] ?? 'Default service description';
                $price_unit = $data['price_unit'] ?? 'session';

                $barter_service = BarterService::factory()->create([
                    'barter_provider_id' => $user->id,
                    'barter_category_id' => $barter_category_id,
                    'title' => $title,
                    'description' => $description,
                    'price_unit' => $price_unit,
                ]);

                $this->addBarterServiceImages($barter_service);
            }
        });

        $this->command->info("Seeded $service_per_user services per user");
    }

    protected function seedBarterTransactions(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $user = User::isNotAdmin()->inRandomOrder()->first();
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

                if (rand(0, 1) && $user->barter_services->isNotEmpty()) {
                    $barter_invoice->barter_services()->attach(
                        $user->barter_services->pluck('id')->random(rand(1, floor($user->barter_services->count() / 2)))
                    );
                }

                if ($barter_transaction->status === 'awaiting_completed') {
                    $random_id = fake()->randomElement([$barter_transaction->barter_acquirer_id, $barter_transaction->barter_provider_id]);
                    $barter_transaction->timestamps = false;
                    $barter_transaction->update([
                        'awaiting_user_id' => $random_id,
                        'updated_at' => $barter_transaction->created_at,
                    ]);
                    $barter_transaction->timestamps = true;
                }

            }
        }

        $this->command->info("Seeded $count transactions");
    }

    protected function seedBarterReviews()
    {
        BarterTransaction::where('status', 'completed')->inRandomOrder()->each(function (BarterTransaction $barter_transaction) {
            if (rand(0, 1)) {
                BarterReview::factory()->create([
                    'reviewer_id' => $barter_transaction->barter_acquirer_id,
                    'barter_transaction_id' => $barter_transaction->id,
                ]);
            }

            if (rand(0, 1)) {
                BarterReview::factory()->create([
                    'reviewer_id' => $barter_transaction->barter_provider_id,
                    'barter_transaction_id' => $barter_transaction->id,
                ]);
            }
        });

        $this->command->info('Seeded reviews');
    }

    protected function seedUserReports(int $count_per_user)
    {
        $user_report_reason = UserReportReason::all();

        User::isNotAdmin()->each(function (User $user) use ($count_per_user, $user_report_reason) {
            $user_report_reason_id = $user_report_reason->random();

            UserReport::factory($count_per_user)->create([
                'reporter_id' => $user->id,
                'user_report_reason_id' => $user_report_reason_id,
            ]);
        });

        $this->command->info("Seeded $count_per_user reports per user");
    }

    protected function addUserAvatar($user, string $gender)
    {
        $path = public_path("/seeders/user_avatar/$gender");

        if (File::exists($path)) {
            $files = File::files($path);

            if (! empty($files)) {
                $file = $files[array_rand($files)];
                $user
                    ->addMedia($file)
                    ->preservingOriginal()
                    ->toMediaCollection('user_avatar');

                return;

            } else {
                $this->command->warn("File does not exist: $path");
            }
        } else {
            $this->command->warn("Directory does not exist: $path");
        }

        $user
            ->addMedia(config('app.default.image.path'))
            ->preservingOriginal()
            ->toMediaCollection('barter_service_images');
    }

    protected function addChatUser($user)
    {
        $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));

        $chat_client->upsertUsers([
            [
                'id' => (string) $user->id,
                'name' => $user->name,
                'role' => 'user',
                'image' => $user->avatar['uri'],
            ],
        ]);
    }

    protected function addBarterServiceImages($barter_service)
    {
        $category = $barter_service->barter_category->name;
        $path = public_path("/seeders/barter_service_images/$category");

        if (File::exists($path)) {
            $files = File::files($path);
            $files = fake()->randomElements($files, 5, false);

            if (! empty($files)) {
                foreach ($files as $file) {
                    $barter_service
                        ->addMedia($file)
                        ->preservingOriginal()
                        ->toMediaCollection('barter_service_images');
                }

                return;

            } else {
                $this->command->warn("File does not exist: $path");
            }
        } else {
            $this->command->warn("Directory does not exist: $path");
        }

        for ($i = 0; $i < 5; $i++) {
            $barter_service
                ->addMedia(config('app.default.image.path'))
                ->preservingOriginal()
                ->toMediaCollection('barter_service_images');
        }
    }
}
