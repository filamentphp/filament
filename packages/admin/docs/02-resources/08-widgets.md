---
title: Widgets
---

## Getting started

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
        Widgets\CustomerOverview::class,
    ];
}
```

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

## Accessing the current record

If you're using a widget on an [Edit](editing-records) or [View](viewing-records) page, you may access the current record by defining a `$record` property on the widget class:

```php
use Illuminate\Database\Eloquent\Model;

public ?Model $record = null;
```
