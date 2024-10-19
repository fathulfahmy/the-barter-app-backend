<?php

namespace App;
use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{
    public static function success(string $message = 'Success', int $status = 200, mixed $data = []): JsonResponse
    {
        return new self([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(string $message = 'Something went wrong', int $status = 400, array $errors = []): JsonResponse
    {
        return new self([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
