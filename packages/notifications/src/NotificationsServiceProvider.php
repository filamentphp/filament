<?php

namespace Filament\Notifications;

use Filament\Notifications\Http\Middleware\InitializeNotificationMailbox;
use Filament\Notifications\Http\Middleware\PublishNotifications;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Session\Middleware\StartSession;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notifications')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(NotificationMailbox::class);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('notifications', __DIR__ . '/../dist/index.js'),
        ], 'filament/notifications');

        Livewire::component('database-notifications', DatabaseNotifications::class);

        Livewire::component('notifications', Notifications::class);

        $this->callAfterResolving(Kernel::class, static function (Kernel $kernel): void {
            $kernel->prependMiddleware(PublishNotifications::class);
            $kernel->addToMiddlewarePriorityAfter(StartSession::class, InitializeNotificationMailbox::class);
        });

        // Include standalone Livewire apps, panel routes, and custom `web` routes.
        $this->app['events']->listen(RouteMatched::class, static function (RouteMatched $event): void {
            if (! in_array(InitializeNotificationMailbox::class, $event->route->middleware(), strict: true)) {
                $event->route->middleware(InitializeNotificationMailbox::class);
                $event->route->computedMiddleware = null;
            }
        });

        $this->app['events']->listen(Login::class, static function (): void {
            app(NotificationMailbox::class)->rotateScope(shouldPreservePublishedNotifications: true);
        });

        $this->app['events']->listen(Logout::class, static function (): void {
            app(NotificationMailbox::class)->rotateScope(shouldPreservePublishedNotifications: true);
        });

        Testable::mixin(new TestsNotifications);
    }
}
