<?php

namespace Mbox\BackCore\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControl
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check())
            return response()->json([
                'message' => __('auth.unauthenticated')
            ], Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
