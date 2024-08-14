<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $request->attributes->set('user', $user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is not valid'], 401);
        }

        return $next($request);
    }
}
