---
title: Getting started
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

<LaracastsBanner
    title="Table Filters"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you the basics of adding filters to Filament resource tables."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/10"
    series="rapid-laravel-development"
/>

Filters allow you to define certain constraints on your data, and allow users to scope it to find the information they need. You put them in the `$table->filters()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ]);
}
```

<AutoScreenshot name="tables/filters/simple" alt="Table with filter" version="3.x" />

Filters may be created using the static `make()` method, passing its unique name. You should then pass a callback to `query()` which applies your filter's scope:

```php
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('is_featured')
    ->query(fn (Builder $query): Builder => $query->where('is_featured', true))
```

## Available filters

By default, using the `Filter::make()` method will render a checkbox form component. When the checkbox is on, the `query()` will be activated.

- You can also [replace the checkbox with a toggle](#using-a-toggle-button-instead-of-a-checkbox).
- You can use a [ternary filter](ternary) to replace the checkbox with a select field to allow users to pick between 3 states - usually "true", "false" and "blank". This is useful for filtering boolean columns that are nullable.
- The [trashed filter](ternary#filtering-soft-deletable-records) is a pre-built ternary filter that allows you to filter soft-deletable records.
- You may use a [select filter](select) to allow users to select from a list of options, and filter using the selection.
- You may use a [query builder](query-builder) to allow users to create complex sets of filters, with an advanced user interface for combining constraints.
- You may build [custom filters](custom) with other form fields, to do whatever you want.

## Setting a label

By default, the label of the filter, which is displayed in the filter form, is generated from the name of the filter. You may customize this using the `label()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')
    ->label('Featured')
```

Optionally, you can have the label automatically translated [using Laravel's localization features](https://laravel.com/docs/localization) with the `translateLabel()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')
    ->translateLabel() // Equivalent to `label(__('Is featured'))`
```

## Customizing the filter form

By default, creating a filter with the `Filter` class will render a [checkbox form component](../../forms/fields/checkbox). When the checkbox is checked, the `query()` function will be applied to the table's query, scoping the records in the table. When the checkbox is unchecked, the `query()` function will be removed from the table's query.

Filters are built entirely on Filament's form fields. They can render any combination of form fields, which users can then interact with to filter the table.

### Using a toggle button instead of a checkbox

The simplest example of managing the form field that is used for a filter is to replace the [checkbox](../../forms/fields/checkbox) with a [toggle button](../../forms/fields/toggle), using the `toggle()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')
    ->toggle()
```

<AutoScreenshot name="tables/filters/toggle" alt="Table with toggle filter" version="3.x" />

### Applying the filter by default

You may set a filter to be enabled by default, using the `default()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')
    ->default()
```

### Customizing the built-in filter form field

Whether you are using a checkbox, a [toggle](#using-a-toggle-button-instead-of-a-checkbox) or a [select](select), you can customize the built-in form field used for the filter, using the `modifyFormFieldUsing()` method. The method accepts a function with a `$field` parameter that gives you access to the form field object to customize:

```php
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')
    ->modifyFormFieldUsing(fn (Checkbox $field) => $field->inline(false))
```

## Persist filters in session

To persist the table filters in the user's session, use the `persistFiltersInSession()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->persistFiltersInSession();
}
```

## Deferring filters

You can defer filter changes from affecting the table, until the user clicks an "Apply" button. To do this, use the `deferFilters()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->deferFilters();
}
```

### Customizing the apply filters action

When deferring filters, you can customize the "Apply" button, using the `filtersApplyAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../../actions/trigger-button) can be used:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersApplyAction(
            fn (Action $action) => $action
                ->link()
                ->label('Save filters to table'),
        );
}
```

## Deselecting records when filters change

By default, all records will be deselected when the filters change. Using the `deselectAllRecordsWhenFiltered(false)` method, you can disable this behavior:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->deselectAllRecordsWhenFiltered(false);
}
```

## Modifying the base query

By default, modifications to the Eloquent query performed in the `query()` method will be applied inside a scoped `where()` clause. This is to ensure that the query does not clash with any other filters that may be applied, especially those that use `orWhere()`.

However, the downside of this is that the `query()` method cannot be used to modify the query in other ways, such as removing global scopes, since the base query needs to be modified directly, not the scoped query.

To modify the base query directly, you may use the `baseQuery()` method, passing a closure that receives the base query:

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('trashed')
    // ...
    ->baseQuery(fn (Builder $query) => $query->withoutGlobalScopes([
        SoftDeletingScope::class,
    ]))
```

## Customizing the filters trigger action

To customize the filters trigger buttons, you may use the `filtersTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../../actions/trigger-button) can be used:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersTriggerAction(
            fn (Action $action) => $action
                ->button()
                ->label('Filter'),
        );
}
```

<AutoScreenshot name="tables/filters/custom-trigger-action" alt="Table with custom filters trigger action" version="3.x" />

## Table filter utility injection

The vast majority of methods used to configure filters accept functions as parameters instead of hardcoded values:

```php
use App\Models\Author;
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->options(fn (): array => Author::query()->pluck('name', 'id')->all())
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the current filter instance

If you wish to access the current filter instance, define a `$filter` parameter:

```php
use Filament\Tables\Filters\BaseFilter;

function (BaseFilter $filter) {
    // ...
}
```

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance that the table belongs to, define a `$livewire` parameter:

```php
use Filament\Tables\Contracts\HasTable;

function (HasTable $livewire) {
    // ...
}
```

### Injecting the current table instance

If you wish to access the current table configuration instance that the filter belongs to, define a `$table` parameter:

```php
use Filament\Tables\Table;

function (Table $table) {
    // ...
}
```

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you are able to combine multiple parameters in any order:

```php
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

function (HasTable $livewire, Table $table) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use Filament\Tables\Table;
use Illuminate\Http\Request;

function (Request $request, Table $table) {
    // ...
}
```
