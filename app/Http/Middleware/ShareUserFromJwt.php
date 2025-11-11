<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Helpers\AuthHelper;

class ShareUserFromJwt
{
    public function handle(Request $request, Closure $next)
    {
        $authUser = AuthHelper::getUserInfo($request);
        $isLoggedIn = $authUser !== null;

        view()->share('isLoggedIn', $isLoggedIn);
        view()->share('authUser', $authUser);

        return $next($request);
    }
}
