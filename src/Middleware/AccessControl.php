<?php

namespace App\Http\Middleware;

use App\Models\Security\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $currentRoute = $request->route()->uri;
        $currentMethod = $request->method() === 'PATCH' ? 'PUT' : $request->method();

        if (!$user->hasAccessToRoute($currentRoute, $currentMethod)){
            return response()->json([
                'error' => __('services.resource.access_denied'),
            ], RESPONSE::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
