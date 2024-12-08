<?php

namespace App;

use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    public static function success(string $message = 'Success', int $status = 200, mixed $data = []): JsonResponse
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message = 'Something went wrong', int $status = 400, array $errors = []): JsonResponse
    {
        return new self([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
