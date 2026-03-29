<?php

namespace App\Providers;

use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Auth;
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
        if (! request()?->query('no_auto_login')) {
            Auth::loginUsingId(1);
        }

        if (request()?->query('notificationAlignment')) {
            Notifications::alignment(match (request()->query('notificationAlignment')) {
                'start' => Alignment::Start,
                'center' => Alignment::Center,
                'end' => Alignment::End,
                'left' => Alignment::Left,
                default => Alignment::Right,
            });
        }

        if (request()?->query('notificationVerticalAlignment')) {
            Notifications::verticalAlignment(match (request()->query('notificationVerticalAlignment')) {
                'center' => VerticalAlignment::Center,
                'end' => VerticalAlignment::End,
                default => VerticalAlignment::Start,
            });
        }
    }
}
