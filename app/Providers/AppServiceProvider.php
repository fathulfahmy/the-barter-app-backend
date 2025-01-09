<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer'),
            );
        });

        Response::macro('apiSuccess', function (string $message = '', mixed $data = [], int $status = 200): JsonResponse {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $status);
        });

        Response::macro('apiError', function (string $message = '', mixed $errors = [], int $status = 500): JsonResponse {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
    }
}
