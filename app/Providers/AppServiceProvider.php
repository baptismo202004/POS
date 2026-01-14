<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Support\Access;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade conditional: @canAccess('module','required')
        Blade::if('canAccess', function (string $module, string $required = 'view') {
            return Access::can(Auth::user(), $module, $required);
        });

        if ($this->app->bound('router')) {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
        }
    }
}
