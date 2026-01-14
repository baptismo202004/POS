<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Allow only authenticated users with userType name 'Admin'
        if (!$user || !optional($user->userType)->name || strcasecmp($user->userType->name, 'Admin') !== 0) {
            // Redirect unauthorized users
            return redirect()->route('dashboard')->with('error', 'Unauthorized: Admin access required.');
        }
        return $next($request);
    }
}
