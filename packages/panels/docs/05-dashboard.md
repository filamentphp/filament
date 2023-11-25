---
title: Dashboard
---

## Overview

Filament allows you to build dynamic dashboards, comprised of "widgets", very easily.

The following document will explain how to use these widgets to assemble a dashboard using the panel.

## Available widgets

Filament ships with these widgets:

- [Stats overview](../widgets/stats-overview) widgets display any data, often numeric data, as stats in a row.
- [Chart](../widgets/charts) widgets display numeric data in a visual chart.
- [Table](#table-widgets) widgets which display a [table](../tables/getting-started) on your dashboard.

You may also [create your own custom widgets](#custom-widgets) which can then have a consistent design with Filament's prebuilt widgets.

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

### Responsive widget widths

You may wish to change the widget width based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that the widget should occupy at each breakpoint:

```php
protected int | string | array $columnSpan = [
    'md' => 2,
    'xl' => 3,
];
```

This is especially useful when using a [responsive widgets grid](#responsive-widgets-grid).

## Customizing the widgets' grid

You may change how many grid columns are used to display widgets.

Firstly, you must [replace the original Dashboard page](#customizing-the-dashboard-page).

Now, in your new `app/Filament/Pages/Dashboard.php` file, you may override the `getColumns()` method to return a number of grid columns to use:

```php
public function getColumns(): int | string | array
{
    return 2;
}
```

### Responsive widgets grid

You may wish to change the number of widget grid columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that should be used at each breakpoint:

```php
public function getColumns(): int | string | array
{
    return [
        'md' => 4,
        'xl' => 5,
    ];
}
```

This pairs well with [responsive widget widths](#responsive-widget-widths).

## Conditionally hiding widgets

You may override the static `canView()` method on widgets to conditionally hide them:

```php
public static function canView(): bool
{
    return auth()->user()->isAdmin();
}
```

## Table widgets

You may easily add tables to your dashboard. Start by creating a widget with the command:

```bash
php artisan make:filament-widget LatestOrders --table
```

You may now [customize the table](../tables/getting-started) by editing the widget file.

## Custom widgets

To get started building a `BlogPostsOverview` widget:

```bash
php artisan make:filament-widget BlogPostsOverview
```

This command will create two files - a widget class in the `/Widgets` directory of the Filament directory, and a view in the `/widgets` directory of the Filament views directory.

## Disabling the default widgets

By default, two widgets are displayed on the dashboard. These widgets can be disabled by updating the `widgets()` array of the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->widgets([]);
}
```

## Customizing the dashboard page

If you want to customize the dashboard class, for example, to [change the number of widget columns](#customizing-widget-width), create a new file at `app/Filament/Pages/Dashboard.php`:

```php
<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    // ...
}
```

Finally, remove the original `Dashboard` class from [configuration file](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->pages([]);
}
```

### Creating multiple dashboards

If you want to create multiple dashboards, you can do so by repeating [the process described above](#customizing-the-dashboard-page). Creating new pages that extend the `Dashboard` class will allow you to create as many dashboards as you need.

You will also need to define the URL path to the extra dashboard, otherwise it will be at `/`:

```php
protected static string $routePath = 'finance';
```

You may also customize the title of the dashboard by overriding the `$title` property:

```php
protected static ?string $title = 'Finance dashboard';
```
