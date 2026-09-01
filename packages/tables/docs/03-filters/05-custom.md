---
title: Custom filters
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Custom filter schemas

You may use [schema components](../../schemas) to create custom filters. The data from the custom filter schema is available in the `$data` array of the `query()` callback:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

Filter::make('created_at')
    ->schema([
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

<UtilityInjection set="formFields" version="4.x" extras="Query;;Illuminate\Database\Eloquent\Builder;;$query;;The Eloquent query builder to modify.||Data;;array<string, mixed>;;$data;;The data from the filter's form fields.">The `query()` function can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/filters/custom-form" alt="Table with custom filter schema" version="4.x" />

### Setting default values for custom filter fields

To customize the default value of a field in a custom filter schema, you may use the `default()` method:

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

Filter::make('created_at')
    ->schema([
        DatePicker::make('created_from'),
        DatePicker::make('created_until')
            ->default(now()),
    ])
```

## Active indicators

When a filter is active, an indicator is displayed above the table content to signal that the table query has been scoped.

<AutoScreenshot name="tables/filters/indicators" alt="Table with filter indicators" version="4.x" />

By default, the label of the filter is used as the indicator. You can override this using the `indicator()` method:

```php
use Filament\Tables\Filters\Filter;

Filter::make('is_admin')
    ->label('Administrators only?')
    ->indicator('Administrators')
```

If you are using a [custom filter schema](#custom-filter-schemas), you should use [`indicateUsing()`](#custom-active-indicators) to display an active indicator.

Please note: if you do not have an indicator for your filter, then the badge-count of how many filters are active in the table will not include that filter.

### Custom active indicators

Not all indicators are simple, so you may need to use `indicateUsing()` to customize which indicators should be shown at any time.

For example, if you have a custom date filter, you may create a custom indicator that formats the selected date:

```php
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

Filter::make('created_at')
    ->schema([DatePicker::make('date')])
    // ...
    ->indicateUsing(function (array $data): ?string {
        if (! $data['date']) {
            return null;
        }

        return 'Created at ' . Carbon::parse($data['date'])->toFormattedDateString();
    })
```

### Multiple active indicators

You may even render multiple indicators at once, by returning an array of `Indicator` objects. If you have different fields associated with different indicators, you should set the field using the `removeField()` method on the `Indicator` object to ensure that the correct field is reset when the filter is removed:

```php
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;

Filter::make('created_at')
    ->schema([
        DatePicker::make('from'),
        DatePicker::make('until'),
    ])
    // ...
    ->indicateUsing(function (array $data): array {
        $indicators = [];

        if ($data['from'] ?? null) {
            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                ->removeField('from');
        }

        if ($data['until'] ?? null) {
            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                ->removeField('until');
        }

        return $indicators;
    })
```

### Preventing indicators from being removed

You can prevent users from removing an indicator using `removable(false)` on an `Indicator` object:

```php
use Carbon\Carbon;
use Filament\Tables\Filters\Indicator;

Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
    ->removable(false)
```
