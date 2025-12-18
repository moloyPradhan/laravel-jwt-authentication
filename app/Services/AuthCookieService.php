<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class AuthCookieService
{
    /**
     * Set these properly in production
     */
    protected bool $secure   = false; // true in HTTPS
    protected bool $httpOnly = true;
    protected string $sameSite = 'Lax';

    /**
     * Base cookie creator
     */
    protected function make(
        string $name,
        string $value,
        int $minutes
    ) {
        return Cookie::make(
            $name,
            $value,
            $minutes,
            '/',
            null,
            $this->secure,
            $this->httpOnly,
            false,
            $this->sameSite
        );
    }

    /* =====================================================
       AUTH TOKENS (JWT) – FOR LOGGED-IN USERS ONLY
    ===================================================== */

    public function makeAccessToken(string $token, int $ttl)
    {
        return $this->make('access_token', $token, $ttl);
    }

    public function makeRefreshToken(string $token, int $ttl)
    {
        return $this->make('refresh_token', $token, $ttl);
    }

    /* =====================================================
       GUEST UID COOKIE (SEPARATE – IMPORTANT)
    ===================================================== */

    /**
     * Persist guest UID (30 days)
     */
    public function setGuestUid(string $guestUid): void
    {
        Cookie::queue(
            Cookie::make(
                'guest_uid',
                $guestUid,
                60 * 24 * 30, // 30 days
                '/',
                null,
                $this->secure,
                false, // readable by JS if needed
                false,
                $this->sameSite
            )
        );
    }

    /**
     * Get guest UID from request
     */
    public function getGuestUid($request): ?string
    {
        return $request->cookie('guest_uid');
    }

    /**
     * Forget guest UID (on login / logout)
     */
    public function forgetGuestUid(): void
    {
        Cookie::queue(Cookie::forget('guest_uid'));
    }

    /* =====================================================
       CLEAR ALL AUTH COOKIES
    ===================================================== */

    public function forgetAll(): array
    {
        return [
            Cookie::forget('access_token'),
            Cookie::forget('refresh_token'),
            Cookie::forget('guest_uid'),
        ];
    }
}
