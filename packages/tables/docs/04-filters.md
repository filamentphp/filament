---
title: Filters
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

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

By default, creating a filter with the `Filter` class will render a [checkbox form component](../forms/fields/checkbox). When the checkbox is checked, the `query()` function will be applied to the table's query, scoping the records in the table. When the checkbox is unchecked, the `query()` function will be removed from the table's query.

Filters are built entirely on Filament's form fields. They can render any combination of form fields, which users can then interact with to filter the table.

### Using a toggle button instead of a checkbox

The simplest example of managing the form field that is used for a filter is to replace the [checkbox](../forms/fields/checkbox) with a [toggle button](../forms/fields/toggle), using the `toggle()` method:

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

### Select filters

Often, you will want to use a [select field](../forms/fields/select) instead of a checkbox. This is especially true when you want to filter a column based on a set of pre-defined options that the user can choose from. To do this, you can create a filter using the `SelectFilter` class:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

The `options()` that are passed to the filter are the same as those that are passed to the [select field](../forms/fields/select).

#### Customizing the column used by a select filter

Select filters do not require a custom `query()` method. The column name used to scope the query is the name of the filter. To customize this, you may use the `attribute()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->attribute('status_id')
```

#### Multi-select filters

These allow the user to select multiple options to apply the filter to their table. For example, a status filter may present the user with a few status options to pick from and filter the table using. When the user selects multiple options, the table will be filtered to show records that match any of the selected options. You can enable this behaviour using the `multiple()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->multiple()
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

#### Relationship select filters

Select filters are also able to automatically populate themselves based on a relationship. For example, if your table has a `author` relationship with a `name` column, you may use `relationship()` to filter the records belonging to an author:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name')
```

### Preloading the select filter relationship options

If you'd like to populate the searchable options from the database when the page is loaded, instead of when the user searches, you can use the `preload()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name')
    ->searchable()
    ->preload()
```

##### Customizing the select filter relationship query

You may customize the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

SelectFilter::make('author')
    ->relationship('author', 'name', fn (Builder $query) => $query->withTrashed())
```

#### Searching select filter options

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name')
    ->searchable()
```

### Ternary filters

Ternary filters allow you to easily create a [select filter](#select-filters) which has three states - usually true, false and blank. To filter a column named `is_admin` to be `true` or `false`, you may use the ternary filter:

```php
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('is_admin')
```

### Using a ternary filter with a nullable column

Another common pattern is to use a nullable column. For example, when filtering verified and unverified users using the `email_verified_at` column, unverified users have a null timestamp in this column. To apply that logic, you may use the `nullable()` method:

```php
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('email_verified_at')
    ->nullable()
```

#### Customizing the column used by a ternary filter

The column name used to scope the query is the name of the filter. To customize this, you may use the `attribute()` method:

```php
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('verified')
    ->nullable()
    ->attribute('status_id')
```

#### Customizing the ternary filter option labels

You may customize the labels used for each state of the ternary filter. The true option label can be customized using the `trueLabel()` method. The false option label can be customized using the `falseLabel()` method. The blank (default) option label can be customized using the `placeholder()` method:

```php
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('email_verified_at')
    ->label('Email verification')
    ->nullable()
    ->placeholder('All users')
    ->trueLabel('Verified users')
    ->falseLabel('Not verified users')
```

#### Customizing how a ternary filter modifies the query

You may customize how the query changes for each state of the ternary filter, use the `queries()` method:

```php
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

TernaryFilter::make('trashed')
    ->placeholder('Without trashed records')
    ->trueLabel('With trashed records')
    ->falseLabel('Only trashed records')
    ->queries(
        true: fn (Builder $query) => $query->withTrashed(),
        false: fn (Builder $query) => $query->onlyTrashed(),
        blank: fn (Builder $query) => $query->withoutTrashed(),
    )
```

### Custom filter forms

<LaracastsBanner
    title="Build a Custom Table Filter"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/11"
/>

You may use components from the [Form Builder](../forms/fields/getting-started) to create custom filter forms. The data from the custom filter form is available in the `$data` array of the `query()` callback:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('created_at')
    ->form([
        DatePicker::make('created_from'),
        DatePicker::make('created_until'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    })
```

<AutoScreenshot name="tables/filters/custom-form" alt="Table with custom filter form" version="3.x" />

#### Setting default values for custom filter fields

To customize the default value of a field in a custom filter form, you may use the `default()` method:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

Filter::make('created_at')
    ->form([
        DatePicker::make('created_from'),
        DatePicker::make('created_until')
            ->default(now()),
    ])
```

## Active indicators

When a filter is active, an indicator is displayed above the table content to signal that the table query has been scoped.

<AutoScreenshot name="tables/filters/indicators" alt="Table with filter indicators" version="3.x" />

By default, the label of the filter is used as the indicator. You can override this using the `indicator()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_admin')
    ->label('Administrators only?')
    ->indicator('Administrators')
```

If you are using a [custom filter form](#custom-filter-forms), you should use [`indicateUsing()`](#custom-active-indicators) to display an active indicator.

### Custom active indicators

Not all indicators are simple, so you may need to use `indicateUsing()` to customize which indicators should be shown at any time.

For example, if you have a custom date filter, you may create a custom indicator that formats the selected date:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

Filter::make('created_at')
    ->form([DatePicker::make('date')])
    // ...
    ->indicateUsing(function (array $data): ?string {
        if (! $data['date']) {
            return null;
        }

        return 'Created at ' . Carbon::parse($data['date'])->toFormattedDateString();
    })
```

### Multiple active indicators

You may even render multiple indicators at once, by returning an array. If you have different fields associated with different indicators, you should use the field's name as the array key, to ensure that the correct field is reset when the filter is removed:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

Filter::make('created_at')
    ->form([
        DatePicker::make('from'),
        DatePicker::make('until'),
    ])
    // ...
    ->indicateUsing(function (array $data): array {
        $indicators = [];

        if ($data['from'] ?? null) {
            $indicators['from'] = 'Created from ' . Carbon::parse($data['from'])->toFormattedDateString();
        }

        if ($data['until'] ?? null) {
            $indicators['until'] = 'Created until ' . Carbon::parse($data['until'])->toFormattedDateString();
        }

        return $indicators;
    })
```

## Positioning filters into grid columns

To change the number of columns that filters may occupy, you may use the `filtersFormColumns()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormColumns(3);
}
```

## Controlling the width of the filters dropdown

To customize the dropdown width, you may use the `filtersFormWidth()` method, and specify a width - `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, or `7xl`. By default, the width is `xs`:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormWidth('4xl');
}
```

## Controlling the maximum height of the filters dropdown

To add a maximum height to the filters' dropdown content, so that they scroll, you may use the `filtersFormMaxHeight()` method, passing a [CSS length](https://developer.mozilla.org/en-US/docs/Web/CSS/length):

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormMaxHeight('400px');
}
```

## Displaying filters above the table content

To render the filters above the table content instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::AboveContent);
}
```

<AutoScreenshot name="tables/filters/above-content" alt="Table with filters above content" version="3.x" />

### Allowing filters above the table content to be collapsed

To allow the filters above the table content to be collapsed, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::AboveContentCollapsible);
}
```

## Displaying filters below the table content

To render the filters below the table content instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::BelowContent);
}
```

<AutoScreenshot name="tables/filters/below-content" alt="Table with filters below content" version="3.x" />

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

## Deselecting records when filters change

By default, all records will be deselected when the filters change. Using the `deselectAllRecordsWhenFiltered(false)` method, you can disable this behaviour:

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

## Customizing the filters dropdown trigger action

To customize the filters' dropdown trigger buttons, you may use the `filtersTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../actions/trigger-button) can be used:

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
