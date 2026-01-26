<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Support\Access;
use App\Models\Purchase;
use App\Observers\PurchaseObserver;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Blade conditional: @canAccess('module','required')
        Blade::if('canAccess', function (string $module, string $required = 'view') {
            return Access::can(Auth::user(), $module, $required);
        });

        Purchase::observe(PurchaseObserver::class);

        // Expense Gates
        Gate::define('update-expense', function (User $user, Expense $expense) {
            return is_null($expense->purchase_id);
        });

        Gate::define('delete-expense', function (User $user, Expense $expense) {
            return is_null($expense->purchase_id);
        });

        if ($this->app->bound('router')) {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
        }
    }
}
