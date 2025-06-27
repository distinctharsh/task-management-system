<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIPAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        $allowedIPs = config('app.allowed_ips', []);
        $clientIP = $request->ip();

        // If IP matches and user is not authenticated, allow access to history page
        if (in_array($clientIP, $allowedIPs) && !$request->user()) {
            return $next($request);
        }

        // For all other cases, proceed normally (which will require authentication)
        return $next($request);
    }
}
