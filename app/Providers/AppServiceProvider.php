<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

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
    
        if ($this->app->bound('router')) {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
        }
    }
}
