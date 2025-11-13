<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthHelper
{
    /**
     * Get the authenticated user from the JWT token in cookie.
     *
     * @param Request $request
     * @return object|null
     */
    public static function getUser(Request $request)
    {
        $token = $request->cookie('access_token');

        if ($token) {
            try {
                JWTAuth::setToken($token);
                return JWTAuth::authenticate();
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Check if user is logged in.
     *
     * @param Request $request
     * @return bool
     */
    public static function isLoggedIn(Request $request)
    {
        return self::getUser($request) !== null;
    }

    /**
     * Get simplified user details for views
     *
     * @param Request $request
     * @return array|null
     */
    public static function getUserInfo(Request $request)
    {
        $user = self::getUser($request);
        if ($user) {
            return [
                'id'    => $user->id,
                'uid'   => $user->uid,
                'email' => $user->email,
                'name'  => $user->name,
            ];
        }
        return null;
    }

    public static function getUserId(Request $request)
    {
        $user = self::getUser($request);
        if ($user) {
            return $user->uid;
        }
        return null;
    }
}
