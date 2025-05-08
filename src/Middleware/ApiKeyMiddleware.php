<?php

namespace App\Http\Middleware;

use App\Models\Security\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('ENABLE_API_KEY_AUTH', false)) {
            $apiKey = $request->header('X-API-KEY');
            if (!$apiKey){
                return $next($request);
            }

            $keyRecord = ApiKey::where('key', $apiKey)->where('status', true)->first();

            if (!$keyRecord) {
                return response()->json(['message' => __('auth.unauthorized')], 401);
            }

            $user = $keyRecord->users;
            if ($user){
                Auth::setUser($user);
            }
        }

        return $next($request);
    }
}
