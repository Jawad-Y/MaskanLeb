<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_banned) {
            return response()->json([
                'message' => 'Your account has been banned. Please contact support.',
            ], 403);
        }

        return $next($request);
    }
}
