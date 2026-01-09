<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PreventBackHistory
{
    /**
     * Handle an incoming request and prevent browser caching.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof HttpResponse) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
