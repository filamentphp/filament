---
title: Configuration
---

## Overview

By default, the configuration file is located at `app/Providers/Filament/AdminContextProvider.php`. Keep reading to learn more about [contexts](#introducing-contexts) and how each has [its own configuration file](#creating-a-new-context).

## Introducing contexts

By default, when you install the package, there is 1 app (a "context") that has been set up for you - and it lives on `/admin`. All the [resources](resources), [pages](pages), and [dashboard widgets](dashboard) you create get registered to this context.

However, you can create as many contexts as you want, and each can have its own set of resources, pages and widgets.

For example, you could build an app where users can log in at `/app` and access their dashboard, and admins can log in at `/admin` and manage the app. The `/app` context and the `/admin` context have their own resources, since each group of users has different requirements. Filament allows you to do that by providing you with the ability to create multiple contexts.

### The default admin context

When you run `filament:install`, a new file is created in `app/Providers/Filament` - `AdminContextProvider.php`. This is the file contains the configuration for the `/admin` context.

When this documentation refers to the "configuration", this is the file you need to edit. It allows you to completely customize the app.

### Creating a new context

To create a new context, you can use the `make:filament-context` command, passing in the unique name of the new context:

```bash
php artisan make:filament-context app
```

This command will create a new context called "app". A configuration file will be created at `app/Providers/Filament/AppContextProvider.php`. You can access this context at `/app`, but you can [customize the path](#changing-the-path) if you don't want that.

Since this configuration file is also a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get a 404 when trying to access your context then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

## Changing the path

In a context configuration file, you can change the path that the app is accessible at using the `path()` method:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->path('app');
}
```

If you want the app to be accessible without any prefix, you can set this to be an empty string:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->path('');
}
```

## Render hooks

Filament allows you to render Blade content at various points in the app layout. This is useful for integrations with packages like [`wire-elements/modal`](https://github.com/wire-elements/modal) which require you to add a Livewire component to your app.

Here's an example, integrating [`wire-elements/modal`](https://github.com/wire-elements/modal) with Filament:

```php
use Filament\Context;
use Illuminate\Support\Facades\Blade;

public function context(Context $context): Context
{
    return $context
        // ...
        ->renderHook(
            'body.start',
            fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
        );
}
```

You could also render view content from a file:

```php
$context->renderHook(
    'body.start',
    fn (): View => view('impersonation-banner'),
);
```

The available hooks are as follows:

- `body.start` - after `<body>`
- `body.end` - before `</body>`
- `head.start` - after `<head>`
- `head.end` - before `</head>`
- `content.start` - before page content
- `content.end` - after page content
- `footer` - footer content (centered)
- `footer.before` - before footer
- `footer.after` - after footer
- `sidebar.start` - before [sidebar](navigation) content
- `sidebar.end` - after [sidebar](navigation) content
- `sidebar.footer` - pinned to the bottom of the sidebar, below the content
- `scripts.start` - before scripts are defined
- `scripts.end` - after scripts are defined
- `styles.start` - before styles are defined
- `styles.end` - after styles are defined
- `global-search.start` - before [global search](resources/global-search) input
- `global-search.end` - after [global search](resources/global-search) input
- `tenant-menu.start` - before tenant menu
- `tenant-menu.end` - after tenant menu
- `user-menu.start` - before [user menu](navigation#customizing-the-user-menu)
- `user-menu.end` - after [user menu](navigation#customizing-the-user-menu)
- `user-menu.account.before` - before the account item in the [user menu](navigation#customizing-the-user-menu)
- `user-menu.account.after` - after the account item in the [user menu](navigation#customizing-the-user-menu)
- `page.header-widgets.start` - before page header widgets
- `page.header-widgets.end` - after page header widgets
- `page.footer-widgets.start` - before page footer widgets
- `page.footer-widgets.end` - after page footer widgets
- `page.actions.start` - before page actions
- `page.actions.end` - after page actions

## Setting a domain

By default, Filament will respond to requests from all domains. If you'd like to scope it to a specific domain, you can use the `domain()` method, similar to [`Route::domain()` in Laravel](https://laravel.com/docs/routing#route-group-subdomain-routing):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->domain('admin.example.com');
}
```
