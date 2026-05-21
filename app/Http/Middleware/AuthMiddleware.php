<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is not logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
