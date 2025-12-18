<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class OptionalAuthenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Get token exactly like your Authenticate middleware
        $token = $this->getTokenFromRequest($request);

        if ($token) {
            // Set Authorization header so guard can read it
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Decode and attach payload (same as your middleware)
            $jwtPayload = $this->decodeJwtPayload($token);
            if ($jwtPayload) {
                $request->merge(['jwt' => $jwtPayload]);
                $request->attributes->set('jwt_payload', $jwtPayload);
            }

            // Try authenticating user (DO NOT FAIL if invalid)
            try {
                $user = auth('api')->user();
                if ($user) {
                    auth()->setUser($user); 
                }
            } catch (\Throwable $e) {
                // swallow auth errors → guest user
                Log::debug('Optional JWT auth failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }

    /**
     * --- SAME helpers copied from Authenticate ---
     */
    protected function getTokenFromRequest(Request $request): ?string
    {
        if ($token = $request->cookie('access_token')) {
            return $token;
        }

        if ($token = $request->bearerToken()) {
            return $token;
        }

        return $request->query('access_token');
    }

    protected function decodeJwtPayload(string $token): ?array
    {
        try {
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return null;
            }

            $payload = $parts[1];
            $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));

            return json_decode($decoded, true);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
