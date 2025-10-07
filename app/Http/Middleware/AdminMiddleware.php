<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if the user is an admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is an admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Nemate pristup ovoj stranici.');
        }

        return $next($request);
    }
}