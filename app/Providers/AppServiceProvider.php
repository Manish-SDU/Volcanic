<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\UserVolcano;
use App\Observers\UserVolcanoObserver;

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
        UserVolcano::observe(UserVolcanoObserver::class);

        // Admin authorization gates
        Gate::define('create-volcanoes', function ($user) {
            return $user->is_admin;
        });

        Gate::define('view-volcanoes', function ($user) {
            return $user->is_admin;
        });

        Gate::define('delete-volcanoes', function ($user) {
            return $user->is_admin;
        });

        Gate::define('create-achievements', function ($user) {
            return $user->is_admin;
        });

        Gate::define('view-achievements', function ($user) {
            return $user->is_admin;
        });

        Gate::define('delete-achievements', function ($user) {
            return $user->is_admin;
        });

        Gate::define('view-users', function ($user) {
            return $user->is_admin;
        });

        Gate::define('delete-users', function ($user) {
            return $user->is_admin;
        });
    }
}
