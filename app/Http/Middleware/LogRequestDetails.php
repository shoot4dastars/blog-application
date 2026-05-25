<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestDetails
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userId = auth()->id() ?? 'guest';

            // Simple log entry
            Log::info('Request: ' . $request->method() . ' ' . $request->fullUrl() . ' | User: ' . $userId);

        } catch (\Exception $e) {
            // Don't let logging break the application
        }

        return $next($request);
    }
}
