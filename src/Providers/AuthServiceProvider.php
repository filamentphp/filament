<?php

namespace Filament\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Package policy discovery logic
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'Filament\\Policies\\'.class_basename($modelClass).'Policy';
        });

        // Implicitly grant "Super Admin" role all permissions
        Gate::after(function ($user, $ability) {
            return $user->is_super_admin;
        });
    }
}