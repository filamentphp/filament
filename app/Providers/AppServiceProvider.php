<?php

namespace App\Providers;

use App\Models\Program;
use App\Observers\ProgramObserver;
use Illuminate\Support\ServiceProvider;

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
        Program::observe(ProgramObserver::class);
        \App\Models\AuditLog::observe(\App\Observers\AuditLogObserver::class);
        \App\Models\ProgramStatusHistory::observe(\App\Observers\ProgramStatusHistoryObserver::class);
    }
}
