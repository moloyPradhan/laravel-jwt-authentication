<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class AuthCookieService
{
    protected bool $secure;
    protected bool $httpOnly;
    protected string $sameSite;

    public function __construct()
    {
        $this->secure = false;
        $this->httpOnly = true;
        $this->sameSite = 'Lax';
    }

    public function make(string $name, string $value, int $minutes)
    {
        return Cookie::make(
            $name,
            $value,
            $minutes,
            "/",
            null,
            $this->secure,
            $this->httpOnly,
            false,
            $this->sameSite
        );
    }

    public function forget(string $name)
    {
        return Cookie::forget($name);
    }

    public function makeAccessToken(string $token, int $ttl)
    {
        return $this->make('access_token', $token, $ttl);
    }

    public function makeRefreshToken(string $token, int $ttl)
    {
        // Refresh token may require more relaxed SameSite policy if used cross-domain
        return Cookie::make(
            'refresh_token',
            $token,
            $ttl,
            null,
            null,
            $this->secure,
            $this->httpOnly,
            false,
            'Lax'
        );
    }

    public function forgetAll()
    {
        return [
            $this->forget('access_token'),
            $this->forget('refresh_token'),
        ];
    }
}
