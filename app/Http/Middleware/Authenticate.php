<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        // Get token from header or cookie
        $token = $this->getTokenFromRequest($request);

        if ($token) {
            // Set Authorization header for Laravel's auth
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Decode JWT and attach payload to request
            $jwtPayload = $this->decodeJwtPayload($token);
            if ($jwtPayload) {
                $request->merge(['jwt' => $jwtPayload]);
                // Or use request attributes (cleaner approach)
                $request->attributes->set('jwt_payload', $jwtPayload);
            }
        }

        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get token from request (header, cookie, or query)
     */
    protected function getTokenFromRequest(Request $request): ?string
    {
        // Check cookies

        $token = $request->cookie('access_token');
        if ($token) {
            return $token;
        }

        // Check Authorization header
        $token = $request->bearerToken();
        if ($token) {
            return $token;
        }

        // Optional: Check query parameter
        $token = $request->query('access_token');
        if ($token) {
            return $token;
        }

        return null;
    }

    /**
     * Decode JWT payload
     */
    protected function decodeJwtPayload(string $token): ?array
    {
        try {
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return null;
            }

            $payload = $parts[1];

            // Base64 decode (handle URL-safe Base64)
            $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));

            $data = json_decode($decoded, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('JWT decode error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }

    /**
     * Handle unauthenticated requests.
     */
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'success'    => false,
            'httpStatus' => Response::HTTP_UNAUTHORIZED,
            'message'    => 'Unauthenticated',
            'data'       => [],
            'errors'     => ['authentication_failed'],
        ], Response::HTTP_UNAUTHORIZED));
    }
}
