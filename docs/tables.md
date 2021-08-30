---
title: Building Tables
description:
extends: _layouts.documentation
section: content
toc: |
  - [Columns](#columns)
    - [Displaying Relationship Data](#columns-displaying-relationship-data)
    - [Calling Actions](#columns-calling-actions)
    - [Boolean](#columns-boolean)
    - [Icon](#columns-icon)
    - [Image](#columns-image)
    - [Text](#columns-text)
    - [Developing Custom Column Types](#columns-custom-development)
  - [Filters](#filters)
    - [Reusable Filters](#filters-reusable)
  - [Context Customization](#context-customization)
---

# Building Tables

<p class="lg:text-2xl">Filament includes a table builder which can be used to create interactive tables in the admin panel.</p>

Tables have [columns](#columns) and [filters](#filters), which are defined in two methods on the table object.

Here is an example table configuration for a `CustomerResource`:

```php
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

public static function table(Table $table)
{
    return $table
        ->columns([
            Columns\Text::make('name')->primary(),
            Columns\Text::make('email')->url(fn ($customer) => "mailto:{$customer->email}"),
            Columns\Text::make('type')
                ->options([
                    'individual' => 'Individual',
                    'organization' => 'Organization',
                ]),
            Columns\Text::make('birthday')->date(),
            Columns\Boolean::make('is_active')->label('Active?'),
        ])
        ->filters([
            Filter::make('individuals', fn ($query) => $query->where('type', 'individual')),
            Filter::make('organizations', fn ($query) => $query->where('type', 'organization')),
            Filter::make('active', fn ($query) => $query->where('is_active', true)),
        ]);
}
```

## Columns {#columns}

Resource column classes are located in the `Filament\Resources\Tables\Columns` namespace.

All columns have access to the following customization methods:

```php
Column::make($name)
    ->action($action) // Set Livewire action that should be called when this column is clicked. The current record key will be passed in as a parameter.
    ->getValueUsing($callback = fn ($record) => $record->getAttribute('{column name}')) // Set the callback used to retrieve the value of the column from a given record.
    ->label($label) // Set custom label text for with the column header, which is otherwise automatically generated based on its name. It supports localization strings.
    ->primary() // Sets the column as primary, which emphasises it and links to access a record.
    ->searchable() // Allows the values in this column to be searched.
    ->sortable() // Allows the values in this column to be sorted.
    ->url($url, $shouldOpenInNewTab = false); // Set URL callback that should be used to generate a URL to send the user to when this column is clicked.
```

### Displaying Relationship Data {#columns-displaying-relationship-data}

You set up columns that display results from a related model using dot syntax in its name:

```php
Column::make('customer.name');
```

This would check for a `customer` relationship on the parent model and output the related customer's name.

### Calling Actions {#columns-calling-actions}

You may want something to happen when a cell is clicked. Usually, this is opening a URL, or running a custom Livewire action.

To open a URL when a cell is clicked, a callback is used to generate the destination. For example:

```php
Column::make('website')
    ->url(fn ($record) => $record->website, true);
```

Cells of the above column will display the contents of the record's `website`, and redirect the user to it when they click. The second parameter to `url()`, `true`, means that the website will open in a new tab when clicked.

Alternatively, you may specify a custom Livewire action that should run when the column is clicked. The primary key of the clicked record will be passed as a parameter to the action:

```php
Column::make('username')
    ->action('editUsername');
```

Cells of the above column will call the `editUsername()` Livewire action when clicked.

### Boolean {#columns-boolean}

The `trueIcon()` and `falseIcon()` methods support the name of any Blade icon component, and passes a set of formatting classes to it. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
Boolean::make($name)
    ->falseIcon($icon = 'heroicon-o-x-circle') // Set the icon that should be displayed when the cell is false.
    ->trueIcon($icon = 'heroicon-s-check-circle'); // Set the icon that should be displayed when the cell is true.
```

### Icon {#columns-icon}

The `options()` method supports the names of any Blade icon components, and passes a set of formatting classes to them. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
Icon::make($name)
    ->options($options = []); // Set the icon that should be displayed when the cell is a given value.
```

Here is an example usage of this column:

```php
Icon::make('status')
    ->options([
        'heroicon-s-check-circle' => fn ($status) => $status === 'accepted', // When the `status` is `accepted`, render the `check-circle` Heroicon.
        'heroicon-s-x-circle' => fn ($status) => $status === 'declined', // When the `status` is `declined`, render the `x-circle` Heroicon.
        'heroicon-s-clock' => fn ($status) => $status === 'pending', // When the `status` is `pending`, render the `clock` Heroicon.
    ]);
```

### Image {#columns-image}

```php
Image::make($name)
    ->disk($disk) // Set a custom disk that images should be read from.
    ->height($height = 40) // Set the height of the image in pixels.
    ->rounded() // Make the image preview fully rounded.
    ->size($size) // Set the height and width of the image in pixels.
    ->width($width); // Set the width of the image in pixels.
```

### Text {#columns-text}

```php
Text::make($name)
    ->currency($symbol = '$', $decimalSeparator = '.', $thousandsSeparator = ',', $decimals = 2) // Format values in this column in a currency format.
    ->date($format = 'F j, Y') // Format values in this column as dates, using PHP date formatting tokens.
    ->dateTime($format = 'F j, Y H:i:s') // Format values in this column as date-times, using PHP date formatting tokens.
    ->default() // Set the default value for when this field does not exist.
    ->formatUsing($callback = fn ($value) => $value) // Set the callback used to format the value of the column.
    ->limit($limit) // Truncate the value of this column to a certain number of characters.
    ->options($options = []); // Set the key-value array of available values that this column could hold.
```

> Other column types are coming soon. For more information, please see our [Development Roadmap](/docs/roadmap).

### Developing Custom Column Types {#columns-custom-development}

To create a new column type, which may be used in any table, you may generate a class and cell view using:

```bash
php artisan make:filament-column Avatar --resource
```

Alternatively, simple custom columns may be created using a `View` component, and passing the name of a cell `$view` in your app:

```php
Columns\View::make($view)
    ->data($data = []); // Set the key-value array of available data that the view has access to.
```

## Filters {#filters}

Filters are used to scope results in the table. Here is an example of a filter at allows only customers with a `type` of `individual` to be shown in the table:

```php
Filter::make('individuals', fn ($query) => $query->where('type', 'individual'));
```

They have access to the following customization options:

```php
Filter::make($name, $callback = fn ($query) => $query)
    ->label($label); // Set custom label text for with the filter, which is otherwise automatically generated based on its name. It supports localization strings.
```

### Reusable Filters {#filters-reusable}

You may wish to create a filter that you may reuse across multiple tables.

To create a reusable filter, you may use the following command:

```bash
php artisan make:filament-filter ActiveFilter --resource
```

This will create a new filter in the `app/Filament/Resources/Tables/Filters` directory:

```php
<?php

namespace App\Filament\Resources\Tables\Filters;

use Filament\Tables\Filter;

class ActiveFilter extends Filter
{
    protected function setUp()
    {
        $this->name('active');
    }

    public function apply($query)
    {
        return $query;
    }
}
```

You may modify the filter's query in the `apply()` method of that class:

```php
public function apply($query)
{
    return $query->where('is_active', true);
}
```

> Currently, filters are static and only one may be applied at a time. Parameter-based filters and support for applying multiple filters at once is coming soon. For more information, please see our [Development Roadmap](/docs/roadmap).

## Context Customization {#context-customization}

You may customize tables based on the page they are used. To do this, you can chain the `only()` or `except()` methods onto any column or filter.

```php
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;

public static function table(Table $table)
{
    return $table
        ->filters([
            Filter::make('individuals', fn ($customer) => $customer->type === 'individual')
                ->only(Pages\ListCustomers::class),
        ]);
}
```

In this example, the `individuals` filter will `only()` be available on the `ListCustomers` page.

```php
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Table;

public static function table(Table $table)
{
    return $table
        ->columns([
            Columns\Text::make('name')
                ->except(Pages\ListCustomers::class, fn ($column) => $column->primary()),
        ]);
}
```

In this example, the `name` column will be primary, `except()` on the `ListCustomers` page.

This is an incredibly powerful pattern, and allows you to completely customize a table contextually by chaining as many methods as you wish to the callback.
