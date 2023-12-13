---
title: Widgets
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

<LaracastsBanner
    title="Widgets"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you the basics of adding widgets to Filament resources."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/15"
    series="rapid-laravel-development"
/>

Filament allows you to display widgets inside pages, below the header and above the footer.

You can use an existing [dashboard widget](../dashboard), or create one specifically for the resource.

## Creating a resource widget

To get started building a resource widget:

```bash
php artisan make:filament-widget CustomerOverview --resource=CustomerResource
```

This command will create two files - a widget class in the `app/Filament/Resources/CustomerResource/Widgets` directory, and a view in the `resources/views/filament/resources/customer-resource/widgets` directory.

You must register the new widget in your resource's `getWidgets()` method:

```php
public static function getWidgets(): array
{
    return [
        CustomerResource\Widgets\CustomerOverview::class,
    ];
}
```

If you'd like to learn how to build and customize widgets, check out the [Dashboard](../dashboard) documentation section.

## Displaying a widget on a resource page

To display a widget on a resource page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods for that page:

```php
<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;

class ListCustomers extends ListRecords
{
    public static string $resource = CustomerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerResource\Widgets\CustomerOverview::class,
        ];
    }
}
```

`getHeaderWidgets()` returns an array of widgets to display above the page content, whereas `getFooterWidgets()` are displayed below.

If you'd like to customize the number of grid columns used to arrange widgets, check out the [Pages documentation](../pages#customizing-the-widgets-grid).

## Accessing the current record in the widget

If you're using a widget on an [Edit](editing-records) or [View](viewing-records) page, you may access the current record by defining a `$record` property on the widget class:

```php
use Illuminate\Database\Eloquent\Model;

public ?Model $record = null;
```

## Accessing page table data in the widget

If you're using a widget on a [List](listing-records) page, you may access the table data by first adding the `ExposesTableToWidgets` trait to the page class:

```php
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;

    // ...
}
```

Now, on the widget class, you must add the `InteractsWithPageTable` trait, and return the name of the page class from the `getTablePage()` method:

```php
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;

class ProductStats extends Widget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    // ...
}
```

In the widget class, you can now access the Eloquent query builder instance for the table data using the `$this->getPageTableQuery()` method:

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

Stat::make('Total Products', $this->getPageTableQuery()->count()),
```

Alternatively, you can access a collection of the records on the current page using the `$this->getPageTableRecords()` method:

```php
use Filament\Widgets\StatsOverviewWidget\Stat;

Stat::make('Total Products', $this->getPageTableRecords()->count()),
```

## Passing properties to widgets on resource pages

When registering a widget on a resource page, you can use the `make()` method to pass an array of [Livewire properties](https://livewire.laravel.com/docs/properties) to it:

```php
protected function getHeaderWidgets(): array
{
    return [
        CustomerResource\Widgets\CustomerOverview::make([
            'status' => 'active',
        ]),
    ];
}
```

This array of properties gets mapped to [public Livewire properties](https://livewire.laravel.com/docs/properties) on the widget class:

```php
use Filament\Widgets\Widget;

class CustomerOverview extends Widget
{
    public string $status;

    // ...
}
```

Now, you can access the `status` in the widget class using `$this->status`.
