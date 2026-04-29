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
        $roleName = optional(optional($user)->userType)->name ?? '';
        $superRoles = config('rbac.super_roles', []);

        // Allow Admin or any super_role (e.g. Superadmin)
        $allowed = strcasecmp($roleName, 'Admin') === 0
            || in_array($roleName, $superRoles);

        if (! $user || ! $allowed) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized: Admin access required.');
        }

        return $next($request);
    }
}
