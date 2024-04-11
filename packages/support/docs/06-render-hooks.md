---
title: Render hooks
---

## Overview

Filament allows you to render Blade content at various points in the frameworks views. It's useful for plugins to be able to inject HTML into the framework. Also, since Filament does not recommend publishing the views due to an increased risk of breaking changes, it's also useful for users.

## Registering render hooks

To register render hooks, you can call `FilamentView::registerRenderHook()` from a service provider or middleware. The first argument is the name of the render hook, and the second argument is a callback that returns the content to be rendered:

```php
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

FilamentView::registerRenderHook(
    PanelsRenderHook::BODY_START,
    fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
);
```

You could also render view content from a file:

```php
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

FilamentView::registerRenderHook(
    PanelsRenderHook::BODY_START,
    fn (): View => view('impersonation-banner'),
);
```

## Available render hooks

### Panel Builder render hooks

```php
    use Filament\View\PanelsRenderHook;
```

- `PanelsRenderHook::AUTH_LOGIN_FORM_AFTER` - After login form
- `PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE` - Before login form
- `PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER` - After password reset request form
- `PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE` - Before password reset request form
- `PanelsRenderHook::AUTH_PASSWORD_RESET_RESET_FORM_AFTER` - After password reset form
- `PanelsRenderHook::AUTH_PASSWORD_RESET_RESET_FORM_BEFORE` - Before password reset form
- `PanelsRenderHook::AUTH_REGISTER_FORM_AFTER` - After register form
- `PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE` - Before register form
- `PanelsRenderHook::BODY_END` - Before `</body>`
- `PanelsRenderHook::BODY_START` - After `<body>`
- `PanelsRenderHook::CONTENT_END` - After page content, inside `<main>`
- `PanelsRenderHook::CONTENT_START` - Before page content, inside `<main>`
- `PanelsRenderHook::FOOTER` - Footer of the page
- `PanelsRenderHook::GLOBAL_SEARCH_AFTER` - After the [global search](../panels/resources/global-search) container, inside the topbar
- `PanelsRenderHook::GLOBAL_SEARCH_BEFORE` - Before the [global search](../panels/resources/global-search) container, inside the topbar
- `PanelsRenderHook::GLOBAL_SEARCH_END` - The end of the [global search](../panels/resources/global-search) container
- `PanelsRenderHook::GLOBAL_SEARCH_START` - The start of the [global search](../panels/resources/global-search) container
- `PanelsRenderHook::HEAD_END` - Before `</head>`
- `PanelsRenderHook::HEAD_START` - After `<head>`
- `PanelsRenderHook::PAGE_END` - End of the page content container, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER` - After the page footer widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_FOOTER_WIDGETS_BEFORE` - Before the page footer widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER` - After the page header actions, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE` - Before the page header actions, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_HEADER_WIDGETS_AFTER` - After the page header widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE` - Before the page header widgets, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::PAGE_START` - Start of the page content container, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER` - After the resource table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE` - Before the resource table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABS_END` - The end of the filter tabs (after the last tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABS_START` - The start of the filter tabs (before the first tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER` - After the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE` - Before the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_RELATION_MANAGER_AFTER` - After the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or relation manager class
- `PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE` - Before the relation manager table, also [can be scoped](#scoping-render-hooks) to the page or relation manager class
- `PanelsRenderHook::RESOURCE_TABS_END` - The end of the resource tabs (after the last tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::RESOURCE_TABS_START` - The start of the resource tabs (before the first tab), also [can be scoped](#scoping-render-hooks) to the page or resource class
- `PanelsRenderHook::SCRIPTS_AFTER` - After scripts are defined
- `PanelsRenderHook::SCRIPTS_BEFORE` - Before scripts are defined
- `PanelsRenderHook::SIDEBAR_NAV_END` - In the [sidebar](../panels/navigation), before `</nav>`
- `PanelsRenderHook::SIDEBAR_NAV_START` - In the [sidebar](../panels/navigation), after `<nav>`
- `PanelsRenderHook::SIDEBAR_FOOTER` - Pinned to the bottom of the sidebar, below the content
- `PanelsRenderHook::STYLES_AFTER` - After styles are defined
- `PanelsRenderHook::STYLES_BEFORE` - Before styles are defined
- `PanelsRenderHook::TENANT_MENU_AFTER` - After the [tenant menu](../panels/tenancy#customizing-the-tenant-menu)
- `PanelsRenderHook::TENANT_MENU_BEFORE` - Before the [tenant menu](../panels/tenancy#customizing-the-tenant-menu)
- `PanelsRenderHook::TOPBAR_AFTER` - Below the topbar
- `PanelsRenderHook::TOPBAR_BEFORE` - Above the topbar
- `PanelsRenderHook::TOPBAR_END` - End of the topbar container
- `PanelsRenderHook::TOPBAR_START` - Start of the topbar container
- `PanelsRenderHook::USER_MENU_AFTER` - After the [user menu](../panels/navigation#customizing-the-user-menu)
- `PanelsRenderHook::USER_MENU_BEFORE` - Before the [user menu](../panels/navigation#customizing-the-user-menu)
- `PanelsRenderHook::USER_MENU_PROFILE_AFTER` - After the profile item in the [user menu](../panels/navigation#customizing-the-user-menu)
- `PanelsRenderHook::USER_MENU_PROFILE_BEFORE` - Before the profile item in the [user menu](../panels/navigation#customizing-the-user-menu)


### Table Builder render hooks

All these render hooks [can be scoped](#scoping-render-hooks) to any table Livewire component class. When using the Panel Builder, these classes might be the List or Manage page of a resource, or a relation manager. Table widgets are also Livewire component classes.

```php
    use Filament\Tables\View\TablesRenderHook;
```

- `TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER` - After the "select all" and "deselect all" action buttons in the selection indicator bar
- `TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE` - Before the "select all" and "deselect all" action buttons in the selection indicator bar
- `TablesRenderHook::TOOLBAR_END` - The end of the toolbar
- `TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER` - After the [grouping](../tables/grouping) selector
- `TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE` - Before the [grouping](../tables/grouping) selector
- `TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER` - After the [reorder](../tables/advanced#reordering-records) trigger
- `TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE` - Before the [reorder](../tables/advanced#reordering-records) trigger
- `TablesRenderHook::TOOLBAR_SEARCH_AFTER` - After the [search](../tables/getting-started#making-columns-sortable-and-searchable) container
- `TablesRenderHook::TOOLBAR_SEARCH_BEFORE` - Before the [search](../tables/getting-started#making-columns-sortable-and-searchable) container
- `TablesRenderHook::TOOLBAR_START` - The start of the toolbar
- `TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER` - After the [toggle columns](../tables/columns/getting-started#toggling-column-visibility) trigger
- `TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE` - Before the [toggle columns](../tables/columns/getting-started#toggling-column-visibility) trigger


### Widgets render hooks

```php
    use Filament\Widgets\View\WidgetsRenderHook;
```

- `WidgetsRenderHook::TABLE_WIDGET_END` - End of the [table widget](../panels/dashboard#table-widgets), after the table itself, also [can be scoped](#scoping-render-hooks) to the table widget class
- `WidgetsRenderHook::TABLE_WIDGET_START` - Start of the [table widget](../panels/dashboard#table-widgets), before the table itself, also [can be scoped](#scoping-render-hooks) to the table widget class


## Scoping render hooks

Some render hooks can be given a "scope", which allows them to only be output on a specific page or Livewire component. For instance, you might want to register a render hook for just 1 page. To do that, you can pass the class of the page or component as the second argument to `registerRenderHook()`:

```php
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

FilamentView::registerRenderHook(
    PanelsRenderHook::PAGE_START,
    fn (): View => view('warning-banner'),
    scopes: \App\Filament\Resources\UserResource\Pages\EditUser::class,
);
```

You can also pass an array of scopes to register the render hook for:

```php
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

FilamentView::registerRenderHook(
    PanelsRenderHook::PAGE_START,
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
use Filament\View\PanelsRenderHook;

FilamentView::registerRenderHook(
    PanelsRenderHook::PAGE_START,
    fn (): View => view('warning-banner'),
    scopes: \App\Filament\Resources\UserResource::class,
);
```

### Retrieving the currently active scopes inside the render hook

The `$scopes` are passed to the render hook function, and you can use them to determine which page or component the render hook is being rendered on:

```php
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

FilamentView::registerRenderHook(
    PanelsRenderHook::PAGE_START,
    fn (array $scopes): View => view('warning-banner', ['scopes' => $scopes]),
    scopes: \App\Filament\Resources\UserResource::class,
);
```

## Rendering hooks

Plugin developers might find it useful to expose render hooks to their users. You do not need to register them anywhere, simply output them in Blade like so:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_START) }}
```

To provide scope your render hook, you can pass it as the second argument to `renderHook()`. For instance, if your hook is inside a Livewire component, you can pass the class of the component using `static::class`:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_START, scopes: $this->getRenderHookScopes()) }}
```

You can even pass multiple scopes as an array, and all render hooks that match any of the scopes will be rendered:

```blade
{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_START, scopes: [static::class, \App\Filament\Resources\UserResource::class]) }}
```
