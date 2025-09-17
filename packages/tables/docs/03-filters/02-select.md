---
title: Select filters
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Often, you will want to use a [select field](../../forms/fields/select) instead of a checkbox. This is especially true when you want to filter a column based on a set of pre-defined options that the user can choose from. To do this, you can create a filter using the `SelectFilter` class:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

The `options()` that are passed to the filter are the same as those that are passed to the [select field](../../forms/fields/select).

<UtilityInjection set="tableFilters" version="4.x">As well as allowing a static value, the `options()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Customizing the column used by a select filter

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

<UtilityInjection set="tableFilters" version="4.x">As well as allowing a static value, the `attribute()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Multi-select filters

These allow the user to [select multiple options](../../forms/select#multi-select) to apply the filter to their table. For example, a status filter may present the user with a few status options to pick from and filter the table using. When the user selects multiple options, the table will be filtered to show records that match any of the selected options. You can enable this behavior using the `multiple()` method:

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

## Relationship select filters

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

### Filtering empty relationships

By default, upon selecting an option, all records that have an empty relationship will be excluded from the results. If you want to introduce an additional "None" option for the user to select, which will include all records that do not have a relationship, you can use the `hasEmptyOption()` argument of the `relationship()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name', hasEmptyOption: true)
```

You can rename the "None" option using the `emptyRelationshipOptionLabel()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name', hasEmptyOption: true)
    ->emptyRelationshipOptionLabel('No author')
```

### Customizing the select filter relationship query

You may customize the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

SelectFilter::make('author')
    ->relationship('author', 'name', fn (Builder $query) => $query->withTrashed())
```

### Searching select filter options

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')
    ->relationship('author', 'name')
    ->searchable()
```

## Disable placeholder selection

You can remove the placeholder (null option), which disables the filter so all options are applied, using the `selectablePlaceholder()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')
    ->selectablePlaceholder(false)
```

## Applying select filters by default

You may set a select filter to be enabled by default, using the `default()` method. If using a single select filter, the `default()` method accepts a single option value. If using a `multiple()` select filter, the `default()` method accepts an array of option values:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->multiple()
    ->default(['draft', 'reviewing'])
```

<UtilityInjection set="tableFilters" version="4.x">As well as allowing a static value, the `default()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

