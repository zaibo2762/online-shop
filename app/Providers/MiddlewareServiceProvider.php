<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('auth', \App\Http\Middleware\Authenticate::class);
        $this->app['router']->aliasMiddleware('guest', \App\Http\Middleware\RedirectIfAuthenticated::class);
    }
}
