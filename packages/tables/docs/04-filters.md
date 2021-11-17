---
title: Filters
---

## Getting started

Filters allow you to scope the Eloquent query as a way to reduce the number of records in a table.

They reside within the `getTableFilters()` method of your Livewire component:

```php
protected function getTableFilters(): array
{
    return [
        // ...
    ];
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
