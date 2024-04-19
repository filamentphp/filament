---
title: Configuration
---

## Overview

By default, the configuration file is located at `app/Providers/Filament/AdminPanelProvider.php`. Keep reading to learn more about [panels](#introducing-panels) and how each has [its own configuration file](#creating-a-new-panel).

## Introducing panels

By default, when you install the package, there is one panel that has been set up for you - and it lives on `/admin`. All the [resources](resources/getting-started), [custom pages](pages), and [dashboard widgets](dashboard) you create get registered to this panel.

However, you can create as many panels as you want, and each can have its own set of resources, pages and widgets.

For example, you could build a panel where users can log in at `/app` and access their dashboard, and admins can log in at `/admin` and manage the app. The `/app` panel and the `/admin` panel have their own resources, since each group of users has different requirements. Filament allows you to do that by providing you with the ability to create multiple panels.

### The default admin panel

When you run `filament:install`, a new file is created in `app/Providers/Filament` - `AdminPanelProvider.php`. This file contains the configuration for the `/admin` panel.

When this documentation refers to the "configuration", this is the file you need to edit. It allows you to completely customize the app.

### Creating a new panel

To create a new panel, you can use the `make:filament-panel` command, passing in the unique name of the new panel:

```bash
php artisan make:filament-panel app
```

This command will create a new panel called "app". A configuration file will be created at `app/Providers/Filament/AppPanelProvider.php`. You can access this panel at `/app`, but you can [customize the path](#changing-the-path) if you don't want that.

Since this configuration file is also a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `bootstrap/providers.php` (Laravel 11 and above) or `config/app.php` (Laravel 10 and below). Filament will attempt to do this for you, but if you get an error while trying to access your panel then this process has probably failed.

## Changing the path

In a panel configuration file, you can change the path that the app is accessible at using the `path()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->path('app');
}
```

If you want the app to be accessible without any prefix, you can set this to be an empty string:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->path('');
}
```

Make sure your `routes/web.php` file doesn't already define the `''` or `'/'` route, as it will take precedence.

## Render hooks

[Render hooks](../support/render-hooks) allow you to render Blade content at various points in the framework views. You can [register global render hooks](../support/render-hooks#registering-render-hooks) in a service provider or middleware, but it also allows you to register render hooks that are specific to a panel. To do that, you can use the `renderHook()` method on the panel configuration object. Here's an example, integrating [`wire-elements/modal`](https://github.com/wire-elements/modal) with Filament:

```php
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->renderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
        );
}
```

A full list of available render hooks can be found [here](../support/render-hooks#available-render-hooks).

## Setting a domain

By default, Filament will respond to requests from all domains. If you'd like to scope it to a specific domain, you can use the `domain()` method, similar to [`Route::domain()` in Laravel](https://laravel.com/docs/routing#route-group-subdomain-routing):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->domain('admin.example.com');
}
```

## Customizing the maximum content width

By default, Filament will restrict the width of the content on the page, so it doesn't become too wide on large screens. To change this, you may use the `maxContentWidth()` method. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge`, `SevenExtraLarge`, `Full`, `MinContent`, `MaxContent`, `FitContent`,  `Prose`, `ScreenSmall`, `ScreenMedium`, `ScreenLarge`, `ScreenExtraLarge` and `ScreenTwoExtraLarge`. The default is `SevenExtraLarge`:

```php
use Filament\Panel;
use Filament\Support\Enums\MaxWidth;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->maxContentWidth(MaxWidth::Full);
}
```

## Lifecycle hooks

Hooks may be used to execute code during a panel's lifecycle. `bootUsing()` is a hook that gets run on every request that takes place within that panel. If you have multiple panels, only the current panel's `bootUsing()` will be run. The function gets run from middleware, after all service providers have been booted:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->bootUsing(function (Panel $panel) {
            // ...
        });
}
```

## SPA mode

SPA mode utilizes [Livewire's `wire:navigate` feature](https://livewire.laravel.com/docs/navigate) to make your server-rendered panel feel like a single-page-application, with less delay between page loads and a loading bar for longer requests. To enable SPA mode on a panel, you can use the `spa()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->spa();
}
```

### Disabling SPA navigation for specific URLs

By default, when enabling SPA mode, any URL that lives on the same domain as the current request will be navigated to using Livewire's [`wire:navigate`](https://livewire.laravel.com/docs/navigate) feature. If you want to disable this for specific URLs, you can use the `spaUrlExceptions()` method:

```php
use App\Filament\Resources\PostResource;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->spa()
        ->spaUrlExceptions(fn (): array => [
            url('/admin'),
            PostResource::getUrl(),
        ]);
}
```

> In this example, we are using [`getUrl()`](/resources/getting-started#generating-urls-to-resource-pages) on a resource to get the URL to the resource's index page. This feature requires the panel to already be registered though, and the configuration is too early in the request lifecycle to do that. You can use a function to return the URLs instead, which will be resolved when the panel has been registered.

These URLs need to exactly match the URL that the user is navigating to, including the domain and protocol. If you'd like to use a pattern to match multiple URLs, you can use an asterisk (`*`) as a wildcard character:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->spa()
        ->spaUrlExceptions([
            '*/admin/posts/*',
        ]);
}
```

## Unsaved changes alerts

You may alert users if they attempt to navigate away from a page without saving their changes. This is applied on [Create](resources/creating-records) and [Edit](resources/editing-records) pages of a resource, as well as any open action modals. To enable this feature, you can use the `unsavedChangesAlerts()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->unsavedChangesAlerts();
}
```

## Enabling database transactions

By default, Filament does not wrap operations in database transactions, and allows the user to enable this themselves when they have tested to ensure that their operations are safe to be wrapped in a transaction. However, you can enable database transactions at once for all operations by using the `databaseTransactions()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->databaseTransactions();
}
```

For any actions you do not want to be wrapped in a transaction, you can use the `databaseTransaction(false)` method:

```php
CreateAction::make()
    ->databaseTransaction(false)
```

And for any pages like [Create resource](resources/creating-records) and [Edit resource](resources/editing-records), you can define the `$hasDatabaseTransactions` property to `false` on the page class:

```php
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected ?bool $hasDatabaseTransactions = false;
    
    // ...
}
```

### Opting in to database transactions for specific actions and pages

Instead of enabling database transactions everywhere and opting out of them for specific actions and pages, you can opt in to database transactions for specific actions and pages.

For actions, you can use the `databaseTransaction()` method:

```php
CreateAction::make()
    ->databaseTransaction()
```

For pages like [Create resource](resources/creating-records) and [Edit resource](resources/editing-records), you can define the `$hasDatabaseTransactions` property to `true` on the page class:

```php
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected ?bool $hasDatabaseTransactions = true;
    
    // ...
}
```

## Registering assets for a panel

You can register [assets](../support/assets) that will only be loaded on pages within a specific panel, and not in the rest of the app. To do that, pass an array of assets to the `assets()` method:

```php
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->assets([
            Css::make('custom-stylesheet', resource_path('css/custom.css')),
            Js::make('custom-script', resource_path('js/custom.js')),
        ]);
}
```

## Applying middleware

You can apply extra middleware to all routes by passing an array of middleware classes to the `middleware()` method in the configuration:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->middleware([
            // ...
        ]);
}
```

By default, middleware will be run when the page is first loaded, but not on subsequent Livewire AJAX requests. If you want to run middleware on every request, you can make it persistent by passing `true` as the second argument to the `middleware()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->middleware([
            // ...
        ], isPersistent: true);
}
```

### Applying middleware to authenticated routes

You can apply middleware to all authenticated routes by passing an array of middleware classes to the `authMiddleware()` method in the configuration:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->authMiddleware([
            // ...
        ]);
}
```

By default, middleware will be run when the page is first loaded, but not on subsequent Livewire AJAX requests. If you want to run middleware on every request, you can make it persistent by passing `true` as the second argument to the `authMiddleware()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->authMiddleware([
            // ...
        ], isPersistent: true);
}
```

## Disabling broadcasting

By default, Laravel Echo will automatically connect for every panel, if credentials have been set up in the [published `config/filament.php` configuration file](installation#publishing-configuration). To disable this automatic connection in a panel, you can use the `broadcasting(false)` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->broadcasting(false);
}
```
