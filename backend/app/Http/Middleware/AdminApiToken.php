<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('app.admin_api_token');
        if (! is_string($expected) || $expected === '') {
            return response()->json(['message' => 'Admin API not configured.'], 503);
        }

        $provided = $request->bearerToken();
        if (! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
