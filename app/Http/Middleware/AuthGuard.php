<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;

class AuthGuard
{
    public function handle(Request $request, Closure $next)
    {
        if (!AuthHelper::isLoggedIn($request)) {
            return redirect()->route('loginPage');
        }
        return $next($request);
    }
}
