<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashier
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Allow only authenticated users with userType name 'Cashier'
        if (!$user || !optional($user->userType)->name || strcasecmp($user->userType->name, 'Cashier') !== 0) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized: Cashier access required.');
        }
        return $next($request);
    }
}
