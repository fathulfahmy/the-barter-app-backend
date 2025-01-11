<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            $request->user()->is_suspended_temporarily ||
            $request->user()->is_suspended_permanently
        ) {
            return response()->json(
                ['message' => 'Your account has been suspended. Please check your email for further details.'],
                409
            );
        }

        return $next($request);
    }
}
