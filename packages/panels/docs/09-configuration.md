---
title: Configuration
---

## Overview

By default, the configuration file is located at `app/Providers/Filament/AdminPanelProvider.php`. Keep reading to learn more about [panels](#introducing-panels) and how each has [its own configuration file](#creating-a-new-panel).

## Introducing panels

By default, when you install the package, there is one panel that has been set up for you - and it lives on `/admin`. All the [resources](resources), [pages](pages), and [dashboard widgets](dashboard) you create get registered to this panel.

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

Since this configuration file is also a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

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

## Render hooks

Filament allows you to render Blade content at various points in the app layout. This is useful for integrations with packages like [`wire-elements/modal`](https://github.com/wire-elements/modal) which require you to add a Livewire component to your app.

Here's an example, integrating [`wire-elements/modal`](https://github.com/wire-elements/modal) with Filament:

```php
use Filament\Panel;
use Illuminate\Support\Facades\Blade;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->renderHook(
            'body.start',
            fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
        );
}
```

You could also render view content from a file:

```php
$panel->renderHook(
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
- `global-search.start` - before [global search](resources/global-search) field
- `global-search.end` - after [global search](resources/global-search) field
- `page.header-widgets.start` - before page header widgets
- `page.header-widgets.end` - after page header widgets
- `page.footer-widgets.start` - before page footer widgets
- `page.footer-widgets.end` - after page footer widgets
- `page.actions.start` - before page actions
- `page.actions.end` - after page actions
- `resource.pages.list-records.table.start` - before the resource table
- `resource.pages.list-records.table.end` - after the resource table
- `resource.relation-manager.start` - before the relation manager table
- `resource.relation-manager.end` - after the relation manager table
- `tenant-menu.start` - before tenant menu
- `tenant-menu.end` - after tenant menu
- `user-menu.start` - before [user menu](navigation#customizing-the-user-menu)
- `user-menu.end` - after [user menu](navigation#customizing-the-user-menu)
- `user-menu.profile.before` - before the profile item in the [user menu](navigation#customizing-the-user-menu)
- `user-menu.profile.after` - after the profile item in the [user menu](navigation#customizing-the-user-menu)

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
