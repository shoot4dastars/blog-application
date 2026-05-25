<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and not active
        if (Auth::check() && !Auth::user()->is_active) {
            // Log the user out
            Auth::logout();

            // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to suspended page with error message
            return redirect()->route('suspended')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
