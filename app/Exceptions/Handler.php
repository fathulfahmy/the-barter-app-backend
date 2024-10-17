<?php

namespace App\Exceptions;

use App\ApiResponse;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     * 
     * @param  \Throwable  $exception
     * @return void
     * 
     * @throws \Exception
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return ApiResponse::error('Validation Error', 422, $exception->errors());
        }

        if ($exception instanceof AuthenticationException) {
            return ApiResponse::error('Unauthorized', 401);
        }

        if ($exception instanceof AuthorizationException) {
            return ApiResponse::error('Forbidden', 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return ApiResponse::error('Not found', 404);
        }

        return ApiResponse::error($exception->getMessage(), 500);
    }
}