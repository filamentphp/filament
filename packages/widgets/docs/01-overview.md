---
title: Overview
---

## Introduction

Filament allows you to build dynamic dashboards, comprised of "widgets". Each widget is an element on the dashboard that displays data in a specific way. For example, you can display [stats](stats-overview), [chart](charts), or a [table](#table-widgets).

## Creating a widget

To create a widget, you can use the `make:filament-widget` command:

```bash
php artisan make:filament-widget MyWidget
```

This command will ask you which type of widget you want to create. You can choose from the following options:

- **Custom**: A custom widget that you can build from scratch.
- **Chart**: A widget that displays a [chart](charts).
- **Stats overview**: A widget that displays [statistics](stats-overview).
- **Table**: A widget that displays a [table](#table-widgets).

## Sorting widgets

Each widget class contains a `$sort` property that may be used to change its order on the page, relative to other widgets:

```php
protected static ?int $sort = 2;
```

## Customizing the dashboard page

If you want to customize the dashboard class, for example, to [change the number of widget columns](#customizing-the-widgets-grid), create a new file at `app/Filament/Pages/Dashboard.php`:

```php
<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // ...
}
```

Finally, remove the original `Dashboard` class from [configuration file](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([]);
}
```

If you don't discover pages with `discoverPages()` in the directory you created the new dashboard class, you should manually register the class in the `pages()` method:

```php
use App\Filament\Pages\Dashboard;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->pages([
            Dashboard::class,
        ]);
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

The primary dashboard shown to a user is the first one they have access to (controlled by [`canAccess()` method](../navigation/custom-pages#authorization)), according to the defined navigation sort order.

The default sort order for dashboards is `-2`. You can control the sort order of custom dashboards with `$navigationSort`:

```php
protected static ?int $navigationSort = 15;
```

### Customizing the widgets' grid

You may change how many grid columns are used to display widgets.

Firstly, you must [replace the original Dashboard page](#customizing-the-dashboard-page).

Now, in your new `app/Filament/Pages/Dashboard.php` file, you may override the `getColumns()` method to return a number of grid columns to use:

```php
public function getColumns(): int | array
{
    return 2;
}
```

#### Responsive widgets grid

You may wish to change the number of widget grid columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that should be used at each breakpoint:

```php
public function getColumns(): int | array
{
    return [
        'md' => 4,
        'xl' => 5,
    ];
}
```

This pairs well with [responsive widget widths](#responsive-widget-widths).

#### Customizing widget width

You may customize the width of a widget using the `$columnSpan` property. You may use a number between 1 and 12 to indicate how many columns the widget should span, or `full` to make it occupy the full width of the page:

```php
protected int | string | array $columnSpan = 'full';
```

##### Responsive widget widths

You may wish to change the widget width based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that the widget should occupy at each breakpoint:

```php
protected int | string | array $columnSpan = [
    'md' => 2,
    'xl' => 3,
];
```

This is especially useful when using a [responsive widgets grid](#responsive-widgets-grid).

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

You may now [customize the table](../tables) by editing the widget file.

## Custom widgets

To get started building a `BlogPostsOverview` widget:

```bash
php artisan make:filament-widget BlogPostsOverview
```

This command will create two files - a widget class in the `/Widgets` directory of the Filament directory, and a view in the `/widgets` directory of the Filament views directory.

The class is a [Livewire component](https://livewire.laravel.com/docs/components), so any Livewire features are available to you. The Blade view can contain any HTML you like, and you can access any public Livewire properties in the view. You can also access the Livewire component instance in the view using `$this`.

## Filtering widget data

You may add a form to the dashboard that allows the user to filter the data displayed across all widgets. When the filters are updated, the widgets will be reloaded with the new data.

Firstly, you must [replace the original Dashboard page](#customizing-the-dashboard-page).

Now, in your new `app/Filament/Pages/Dashboard.php` file, you may add the `HasFiltersForm` trait, and add the `filtersForm()` method to return form components:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        // ...
                    ])
                    ->columns(3),
            ]);
    }
}
```

In widget classes that require data from the filters, you need to add the `InteractsWithPageFilters` trait, which will allow you to use the `$this->pageFilters` property to access the raw data from the filters form:

```php
use App\Models\BlogPost;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class BlogPostsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    public function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return [
            StatsOverviewWidget\Stat::make(
                label: 'Total posts',
                value: BlogPost::query()
                    ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                    ->count(),
            ),
            // ...
        ];
    }
}
```

The `$this->pageFilters` array will always reflect the current form data. Please note that this data is not validated, as it is available live and not intended to be used for anything other than querying the database. You must ensure that the data is valid before using it. In this example, we check if the start date is set before using it in the query.

### Filtering widget data using an action modal

Alternatively, you can swap out the filters form for an action modal, that can be opened by clicking a button in the header of the page. There are many benefits to using this approach:

- The filters form is not always visible, which allows you to use the full height of the page for widgets.
- The filters do not update the widgets until the user clicks the "Apply" button, which means that the widgets are not reloaded until the user is ready. This can improve performance if the widgets are expensive to load.
- Validation can be performed on the filters form, which means that the widgets can rely on the fact that the data is valid - the user cannot submit the form until it is. Canceling the modal will discard the user's changes.

To use an action modal instead of a filters form, you can use the `HasFiltersAction` trait instead of `HasFiltersForm`. Then, register the `FilterAction` class as an action in `getHeaderActions()`:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;
    
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    DatePicker::make('startDate'),
                    DatePicker::make('endDate'),
                    // ...
                ]),
        ];
    }
}
```

Handling data from the filter action is the same as handling data from the filters header form, except that the data is validated before being passed to the widget. The `InteractsWithPageFilters` trait still applies.

### Persisting widget filters in the user's session

By default, the dashboard filters applied will persist in the user's session between page loads. To disable this, override the `$persistsFiltersInSession` property in the dashboard page class:

```php
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected bool $persistsFiltersInSession = false;
}
```

Alternatively, override the `persistsFiltersInSession()` method in the dashboard page class:

```php
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function persistsFiltersInSession(): bool
    {
        return false;
    }
}
```

## Disabling the default widgets

By default, two widgets are displayed on the dashboard. These widgets can be disabled by updating the `widgets()` array of the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->widgets([]);
}
```
