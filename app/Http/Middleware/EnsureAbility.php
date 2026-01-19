<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class EnsureAbility
{
    public function handle(Request $request, Closure $next, string $module, string $required = 'view'): Response
    {
        if (!Access::can($request->user(), $module, $required)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['general' => 'Unauthorized: insufficient permissions.']], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized: insufficient permissions.');
        }
        return $next($request);
    }
}
