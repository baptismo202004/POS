<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage on routes: ->middleware('ability:module,required')
 * Example: ->middleware('ability:purchases,edit')
 */
class EnsureAbility
{
    public function handle(Request $request, Closure $next, string $module, string $required = 'view'): Response
    {
        if (!Access::can($request->user(), $module, $required)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized: insufficient permissions.');
        }
        return $next($request);
    }
}
