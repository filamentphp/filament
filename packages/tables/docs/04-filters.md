---
title: Filters
---

## Getting started

Filters allow you to scope the Eloquent query as a way to reduce the number of records in a table.

If you're using the filters in a Livewire component, you can put them in the `getTableFilters()` method:

```php
protected function getTableFilters(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them in the `$table->filters()` method:

```php
public static function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ]);
}
```

Filters may be created using the static `make()` method, passing its name. The name of the filter should be unique. You should then pass a callback to `query()` which applies your filter's scope:

```php
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('is_featured')
    ->query(fn (Builder $query): Builder => $query->where('is_featured', true))
```

### Setting a label

By default, the label of the filter, which is displayed in the filter form, is generated from the name of the filter. You may customize this using the `label()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')->label('Featured')
```

### Default filters

You may set a filter to be enabled by default, using the `default()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_featured')->label('Featured')->default()
```

## Filter forms

By default, filters have two states: enabled and disabled. When the filter is enabled, it is applied to the query. When it is disabled it is not. This is controlled through a checkbox. However, some filters may require extra data input to narrow down the results further. You may use a custom filter form to collect this data.

### Select filters

Select filters allow you to quickly create a filter that allows the user to select an option to apply the filter to their table. For example, a status filter may present the user with a few status options to pick from and filter the table using:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

Select filters do not require a custom `query()` method. The column name used to scope the query is the name of the filter. To customize this, you may use the `column()` method:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->column('status_id')
```

#### Relationship select filters

Select filters are also able to automatically populate themselves based on a `BelongsTo` relationship. For example, if your table has a `author` relationship with a `name` column, you may use `relationship()` to filter the records belonging to an author:

```php
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('author')->relationship('author', 'name')
```

### Multi-select filters

Multi-select filters allow you to quickly create a filter that allows the user to select multiple options to apply the filter to their table. For example, a status filter may present the user with a few status options to pick from and filter the table using:

```php
use Filament\Tables\Filters\MultiSelectFilter;

MultiSelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

Multi-select filters do not require a custom `query()` method. The column name used to scope the query is the name of the filter. To customize this, you may use the `column()` method:

```php
use Filament\Tables\Filters\MultiSelectFilter;

MultiSelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->column('status_id')
```

#### Relationship multi-select filters

Multi-select filters are also able to automatically populate themselves based on a `BelongsTo` relationship. For example, if your table has a `author` relationship with a `name` column, you may use `relationship()` to filter the records belonging to a selection of authors:

```php
use Filament\Tables\Filters\MultiSelectFilter;

MultiSelectFilter::make('author')->relationship('author', 'name')
```

### Custom filter forms

You may use components from the [Form Builder](/docs/forms/fields) to create custom filter forms. The data from the custom filter form is available in the `$data` array of the `query()` callback:

```php
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('created_from'),
        Forms\Components\DatePicker::make('created_until'),
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

### Setting default values

If you wish to set a default filter value, you may use the `default()` method on the form component:

```php
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('created_from'),
        Forms\Components\DatePicker::make('created_until')->default(now()),
    ])
```

## Customizing popover columns

By default, filters are displayed in a thin popover on the right side of the table, in 1 column.

To change the number of columns that filters may occupy, you may use the `getTableFiltersFormColumns()` method:

```php
protected function getTableFiltersFormColumns(): int
{
    return 3;
}
```

Adding more columns to the filter form will automatically widen the popover. To customize the popover width, you may use the `getTableFiltersFormWidth()` method, and specify a width from `xs` to `7xl`:

```php
protected function getTableFiltersFormWidth(): string
{
    return '4xl';
}
```
