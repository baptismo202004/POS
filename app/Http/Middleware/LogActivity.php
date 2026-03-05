<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user();
            if (!$user) {
                return $response;
            }

            if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
                return $response;
            }

            if ($request->is('admin/access/users/*/activity')) {
                return $response;
            }

            $route = $request->route();
            $routeName = $route?->getName();

            ActivityLog::create([
                'user_id' => $user->id,
                'method' => $request->method(),
                'route_name' => $routeName,
                'path' => '/'.$request->path(),
                'url' => $request->fullUrl(),
                'action' => $this->guessAction($routeName, $request),
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'payload' => $this->safePayload($request),
            ]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }

        return $response;
    }

    private function safePayload(Request $request): array
    {
        $data = $request->except([
            'password',
            'password_confirmation',
            '_token',
        ]);

        return [
            'input' => $data,
        ];
    }

    private function guessAction(?string $routeName, Request $request): ?string
    {
        if ($routeName) {
            $parts = explode('.', $routeName);
            return end($parts) ?: $routeName;
        }

        return strtolower($request->method());
    }
}
