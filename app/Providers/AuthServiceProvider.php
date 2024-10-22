<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
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
        Gate::define('create-link-page', function ($user) {
            return $user->subscribed('default') || $user->linkPages()->count() < 2;
        });

        Gate::define('use-custom-link-cards', function ($user) {
            return $user->subscribed('default');
        });
    }
}
