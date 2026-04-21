---
title: Date range filters
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Date range filters allow users to scope records by a start date, an end date, or both. To use one, create a filter using the `DateRangeFilter` class:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
```

This renders two date pickers — "From" and "Until" — in the filter form. When either is filled, the query is scoped using `whereDate` on the filter's attribute. You do not need to provide a custom `query()` callback for the common case.

## Customizing the column used by a date range filter

The column name used to scope the query defaults to the name of the filter. To customize this, you may use the `attribute()` method:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('published')
    ->attribute('published_at')
```

## Customizing the field labels

You may change the label for either picker using `fromLabel()` and `untilLabel()`:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->fromLabel('Start date')
    ->untilLabel('End date')
```

## Rendering both pickers on one line

By default, the two pickers are stacked vertically. You may render them side by side using `inline()`:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->inline()
```

## Disabling the "until" picker

If you only need a lower bound — for example, to filter records created after a certain date — you may hide the "Until" picker using `withoutUntil()`:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->withoutUntil()
```

You may also pass a boolean or a closure to conditionally disable it:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->withoutUntil(fn (): bool => auth()->user()->isRestricted())
```

## Using the custom JS date picker

By default, both pickers use the browser's native date input. You may opt into the custom JavaScript picker by calling `native(false)`:

```php
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->native(false)
```

## Customizing each date picker individually

If you need to configure the "From" or "Until" picker beyond the shared options — for example to set a minimum date, maximum date, or placeholder — you may pass a closure to `modifyFromDatePickerUsing()` or `modifyUntilDatePickerUsing()`. The closure receives the `DatePicker` instance and should return it:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->modifyFromDatePickerUsing(fn (DatePicker $datePicker): DatePicker => $datePicker
        ->minDate('2020-01-01')
        ->placeholder('Start date'),
    )
    ->modifyUntilDatePickerUsing(fn (DatePicker $datePicker): DatePicker => $datePicker
        ->maxDate(now())
        ->placeholder('End date'),
    )
```

## Customizing the query

If you need custom filtering logic — for example to filter on a relationship or a computed column — you may pass a `query()` callback. The `$data` array contains `date_from` and `date_until` keys:

```php
use Filament\Tables\Filters\DateRangeFilter;
use Illuminate\Database\Eloquent\Builder;

DateRangeFilter::make('created_at')
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['date_from'],
                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['date_until'],
                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    })
```
