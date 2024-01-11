---
title: Render hooks
---

## Overview

Filament allows you to render Blade content at various points in the frameworks views. It's useful for plugins to be able to inject HTML into the framework. Also, since Filament does not recommend publishing the views due to an increased risk of breaking changes, it's also useful for users.

## Registering render hooks

To register render hooks, you can call `FilamentView::registerRenderHook()` from a service provider or middleware. The first argument is the name of the render hook, and the second argument is a callback that returns the content to be rendered:

```php
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

FilamentView::registerRenderHook(
    'panels::body.start',
    fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
);
```

You could also render view content from a file:

```php
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

FilamentView::registerRenderHook(
    'panels::body.start',
    fn (): View => view('impersonation-banner'),
);
```

## Available render hooks

### Panel Builder render hooks

- `panels::auth.login.form.after` - After login form
- `panels::auth.login.form.before` - Before login form
- `panels::auth.password-reset.request.form.after` - After password reset request form
- `panels::auth.password-reset.request.form.before` - Before password reset request form
- `panels::auth.password-reset.reset.form.after` - After password reset form
- `panels::auth.password-reset.reset.form.before` - Before password reset form
- `panels::auth.register.form.after` - After register form
- `panels::auth.register.form.before` - Before register form
- `panels::body.end` - Before `</body>`
- `panels::body.start` - After `<body>`
- `panels::content.end` - After page content, inside `<main>`
- `panels::content.start` - Before page content, inside `<main>`
- `panels::footer` - Footer of the page
- `panels::global-search.after` - After the [global search](../panels/resources/global-search) container, inside the topbar
- `panels::global-search.before` - Before the [global search](../panels/resources/global-search) container, inside the topbar
- `panels::global-search.end` - The end of the [global search](../panels/resources/global-search) container
- `panels::global-search.start` - The start of the [global search](../panels/resources/global-search) container
- `panels::head.end` - Before `</head>`
- `panels::head.start` - After `<head>`
- `panels::page.end` - End of the page content container, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.footer-widgets.after` - After the page footer widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.footer-widgets.before` - Before the page footer widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.header.actions.after` - After the page header actions, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.header.actions.before` - Before the page header actions, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.header-widgets.after` - After the page header widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.header-widgets.before` - Before the page header widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::page.start` - Start of the page content container, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::resource.pages.list-records.table.after` - After the resource table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::resource.pages.list-records.table.before` - Before the resource table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::resource.pages.list-records.tabs.end` - The end of the filter tabs (after the last tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::resource.pages.list-records.tabs.start` - The start of the filter tabs (before the first tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `panels::resource.relation-manager.after` - After the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or relation manager class
- `panels::resource.relation-manager.before` - Before the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or relation manager class
- `panels::scripts.after` - After scripts are defined
- `panels::scripts.before` - Before scripts are defined
- `panels::sidebar.nav.end` - In the [sidebar](../panels/navigation), before `</nav>`
- `panels::sidebar.nav.start` - In the [sidebar](../panels/navigation), after `<nav>`
- `panels::sidebar.footer` - Pinned to the bottom of the sidebar, below the content
- `panels::styles.after` - After styles are defined
- `panels::styles.before` - Before styles are defined
- `panels::tenant-menu.after` - After the [tenant menu](../panels/tenancy#customizing-the-tenant-menu)
- `panels::tenant-menu.before` - Before the [tenant menu](../panels/tenancy#customizing-the-tenant-menu)
- `panels::topbar.after` - Below the topbar
- `panels::topbar.before` - Above the topbar
- `panels::topbar.end` - End of the topbar container
- `panels::topbar.start` - Start of the topbar container
- `panels::user-menu.after` - After the [user menu](../panels/navigation#customizing-the-user-menu)
- `panels::user-menu.before` - Before the [user menu](../panels/navigation#customizing-the-user-menu)
- `panels::user-menu.profile.after` - After the profile item in the [user menu](../panels/navigation#customizing-the-user-menu)
- `panels::user-menu.profile.before` - Before the profile item in the [user menu](../panels/navigation#customizing-the-user-menu)

### Table Builder render hooks

All these render hooks [can be scoped](#scoping-render-hooks) to any table Livewire component class. When using the Panel Builder, these classes might be the List or Manage page of a resource, or a relation manager. Table widgets are also Livewire component classes.

- `tables::toolbar.end` - The end of the toolbar
- `tables::toolbar.grouping-selector.after` - After the [grouping](../tables/grouping) selector
- `tables::toolbar.grouping-selector.before` - Before the [grouping](../tables/grouping) selector
- `tables::toolbar.reorder-trigger.after` - After the [reorder](../tables/advanced#reordering-records) trigger
- `tables::toolbar.reorder-trigger.before` - Before the [reorder](../tables/advanced#reordering-records) trigger
- `tables::toolbar.search.after` - After the [search](../tables/getting-started#making-columns-sortable-and-searchable) container
- `tables::toolbar.search.before` - Before the [search](../tables/getting-started#making-columns-sortable-and-searchable) container
- `tables::toolbar.start` - The start of the toolbar
- `tables::toolbar.toggle-column-trigger.after` - After the [toggle columns](../tables/columns/getting-started#toggling-column-visibility) trigger
- `tables::toolbar.toggle-column-trigger.before` - Before the [toggle columns](../tables/columns/getting-started#toggling-column-visibility) trigger

### Widgets render hooks

- `widgets::table-widget.end` - End of the [table widget](../panels/dashboard#table-widgets), after the table itself, also [can be scoped](#scoping-render-hooks) to the table widget class
- `widgets::table-widget.start` - Start of the [table widget](../panels/dashboard#table-widgets), before the table itself, also [can be scoped](#scoping-render-hooks) to the table widget class

## Scoping render hooks

Some render hooks can be given a "scope", which allows them to only be output on a specific page or Livewire component. For instance, you might want to register a render hook for just 1 page. To do that, you can pass the class of the page or component as the second argument to `registerRenderHook()`:

```php
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

FilamentView::registerRenderHook(
    'panels::page.start',
    fn (): View => view('warning-banner'),
    scopes: \App\Filament\Resources\UserResource\Pages\EditUser::class,
);
```

You can also pass an array of scopes to register the render hook for:

```php
use Filament\Support\Facades\FilamentView;

FilamentView::registerRenderHook(
    'panels::page.start',
    fn (): View => view('warning-banner'),
    scopes: [
        \App\Filament\Resources\UserResource\Pages\CreateUser::class,
        \App\Filament\Resources\UserResource\Pages\EditUser::class,
    ],
);
```

Some render hooks for the [Panel Builder](#panel-builder-render-hooks) allow you to scope hooks to all pages in a resource:

```php
use Filament\Support\Facades\FilamentView;

FilamentView::registerRenderHook(
    'panels::page.start',
    fn (): View => view('warning-banner'),
    scopes: \App\Filament\Resources\UserResource::class,
);
```

### Retrieving the currently active scopes inside the render hook

The `$scopes` are passed to the render hook function, and you can use them to determine which page or component the render hook is being rendered on:

```php
use Filament\Support\Facades\FilamentView;

FilamentView::registerRenderHook(
    'panels::page.start',
    fn (array $scopes): View => view('warning-banner', ['scopes' => $scopes]),
    scopes: \App\Filament\Resources\UserResource::class,
);
```

## Rendering hooks

Plugin developers might find it useful to expose render hooks to their users. You do not need to register them anywhere, simply output them in Blade like so:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook('panels::body.start') }}
```

To provide scope your render hook, you can pass it as the second argument to `renderHook()`. For instance, if your hook is inside a Livewire component, you can pass the class of the component using `static::class`:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start', scopes: $this->getRenderHookScopes()) }}
```

You can even pass multiple scopes as an array, and all render hooks that match any of the scopes will be rendered:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start', scopes: [static::class, \App\Filament\Resources\UserResource::class]) }}
```
