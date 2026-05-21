<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
