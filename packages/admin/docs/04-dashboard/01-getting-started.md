---
title: Getting started
---

Filament allows you to build dynamic dashboards, comprised of "widget" cards, very easily.

Widgets are pure [Livewire](https://laravel-livewire.com) components, so may use any features of that package.

Widgets may also be used on [resource pages](../resources/widgets) or other [custom pages](../pages/widgets).

Filament ships with a few pre-built widgets, as well as the ability to create [custom widgets](#custom-widgets):

- [Stats](stats) widgets display any data, often numeric data, within cards in a row.
- [Chart](charts) widgets display numeric data in a visual chart.
- [Table](tables) widgets render data in a table, which supports sorting, searching, filtering, actions, and everything else included within the [table builder](../../tables).

## Sorting widgets

Each widget class contains a `$sort` property that may be used to change its order on the page, relative to other widgets:

```php
protected static ?int $sort = 2;
```

## Customizing widget width

You may customize the width of a widget using the `$columnSpan` property. You may use a number between 1 and 12 to indicate how many columns the widget should span, or `full` to make it occupy the full width of the page:

```php
protected int | string | array $columnSpan = 'full';
```

## Conditionally hiding widgets

You may override the static `canView()` method on widgets to conditionally hide them:

```php
public static function canView(): bool
{
    return auth()->user()->isAdmin();
}
```

## Disabling the default widgets

By default, two widgets are displayed on the dashboard. These widgets can be disabled by updating the `widgets.register` property of the [configuration](installation#publishing-the-configuration) file:

```php
'widgets' => [
    // ...
    'register' => [],
],
```

## Custom widgets

To get started building a `BlogPostsOverview` widget:

```bash
php artisan make:filament-widget BlogPostsOverview
```

This command will create two files - a widget class in the `/Widgets` directory of the Filament directory, and a view in the `/widgets` directory of the Filament views directory.
