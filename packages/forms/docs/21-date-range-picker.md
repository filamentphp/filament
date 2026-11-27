---
title: Date range picker
---

## Introduction

The date range picker allows the user to select a start date and an end date from a calendar:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
```

<AutoScreenshot name="forms/fields/date-range-picker/simple" alt="Date range picker" />

The user selects the start date, then the end date. Until the end date is selected, the calendar highlights the range that would be selected. Two months are displayed side by side on larger screens.

The state of the field is an array with a `start` and an `end` date, such as `['start' => '2026-01-05', 'end' => '2026-01-09']`. It is `null` until both dates are selected. If you are saving the field to an Eloquent model, cast the attribute to an `array`, so that it is stored in a single JSON column:

```php
protected function casts(): array
{
    return [
        'period' => 'array',
    ];
}
```

## Customizing the storage format

By default, both dates are saved in the `Y-m-d` format. You may customize the format of the dates when they are saved, using the `format()` method. This accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->format('d/m/Y')
```

<UtilityInjection set="formFields">As well as allowing a static value, the `format()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

The date range picker selects calendar dates, without a time. Like a [date-only date picker](date-time-picker#timezones-for-date-only-fields), it uses the app's timezone, unless the storage `format()` contains a `U` token.

## Offering preset ranges

You may list common ranges beside the calendar, such as "Past 7 days", using the `presets()` method. The user can select a preset instead of selecting each date:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->presets()
```

<AutoScreenshot name="forms/fields/date-range-picker/presets" alt="Date range picker with presets" />

To choose which presets are offered, and in which order, pass an array of `Preset` enum cases to the `presets()` method:

```php
use Filament\Forms\Components\DateRangePicker;
use Filament\Forms\Components\DateRangePicker\Enums\Preset;

DateRangePicker::make('period')
    ->presets([
        Preset::Today,
        Preset::PastSevenDays,
        Preset::MonthToDate,
    ])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `presets()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

The following presets are available:

| Preset | Range |
| --- | --- |
| `Preset::Today` | Today |
| `Preset::Yesterday` | Yesterday |
| `Preset::WeekToDate` | The first day of the current week to today |
| `Preset::PastSevenDays` | Today and the 6 days before it |
| `Preset::PastThirtyDays` | Today and the 29 days before it |
| `Preset::MonthToDate` | The first day of the current month to today |
| `Preset::PreviousMonth` | The whole of the previous month |
| `Preset::YearToDate` | The first day of the current year to today |

These presets are calculated in the browser, from the user's current date. The first day of the week is the one set by [`firstDayOfWeek()`](#configuring-the-first-day-of-the-week).

A preset is disabled when its range does not respect the [limits of the field](#date-range-picker-validation), and highlighted when it matches the selected range.

### Adding custom presets

You may add your own presets to the array. The key is the label of the preset, and the value is an array with its start and end dates. Each date may be a `DateTime` instance (e.g. `Carbon`), or a string:

```php
use Filament\Forms\Components\DateRangePicker;
use Filament\Forms\Components\DateRangePicker\Enums\Preset;

DateRangePicker::make('period')
    ->presets([
        Preset::Today,
        Preset::PastSevenDays,
        'Previous quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
        'Summer sale' => ['2026-06-21', '2026-07-05'],
    ])
```

Unlike the built-in presets, custom presets are calculated on the server when the field is rendered, using the app's timezone. The time of each date is ignored, and the earlier date is always used as the start of the range.

## Customizing the display format

You may customize the format that the dates are displayed in, separately from the format used when they are saved. For this, use the `displayFormat()` method, which also accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->displayFormat('d/m/Y')
```

<UtilityInjection set="formFields">As well as allowing a static value, the `displayFormat()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/date-range-picker/display-format" alt="Date range picker with a custom display format" />

You may also configure the locale that is used when rendering the calendar and the display, if you want to use a different locale from your app config. For this, you can use the `locale()` method:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->displayFormat('d F Y')
    ->locale('fr')
```

<UtilityInjection set="formFields">As well as allowing a static value, the `locale()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Configuring the first day of the week

In some countries, the first day of the week is not Monday. To customize the first day of the week in the calendar, use the `firstDayOfWeek()` method on the component. 0 to 7 are accepted values, with Monday as 1 and Sunday as 7 or 0:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->firstDayOfWeek(7)
```

There are additionally convenient helper methods to set the first day of the week more semantically:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->weekStartsOnMonday()

DateRangePicker::make('period')
    ->weekStartsOnSunday()
```

## Disabling specific dates

To prevent specific dates from being selected:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('stay')
    ->disabledDates(['2026-12-24', '2026-12-25', '2026-12-31'])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `disabledDates()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/date-range-picker/disabled-dates" alt="Date range picker where dates are disabled" />

Once the start date is selected, a range that would contain a disabled date cannot be selected either.

Disabled dates are only enforced by the calendar, as with the [date picker](date-time-picker#disabling-specific-dates). If you need to reject them on the server, add your own [validation rule](validation#custom-rules).

## Closing the picker when a range is selected

To close the picker when a range is selected, you can use the `closeOnDateSelection()` method. The picker is closed once the end date or a preset is selected:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->closeOnDateSelection()
```

<UtilityInjection set="formFields">As well as allowing a static value, the `closeOnDateSelection()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Focusing a default calendar date

By default, if the field has no state, opening the calendar will show the current month. You can use the `defaultFocusedDate()` method to open the calendar on a specific date instead:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->defaultFocusedDate(now()->startOfYear())
```

<UtilityInjection set="formFields">As well as allowing a static value, the `defaultFocusedDate()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Customizing the loading placeholder

The date range picker displays a loading placeholder while its JavaScript is loaded and initialized. You may customize it using `loadingPlaceholder()`, passing a Blade view or an `Htmlable` instance, such as an `HtmlString` containing trusted HTML:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->loadingPlaceholder(fn () => view('forms.date-range-picker-loading-placeholder'))
```

<UtilityInjection set="formFields">As well as allowing a static value, the `loadingPlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Keep your placeholder HTML decorative and noninteractive. Filament hides it from assistive technologies and supplies an accessible loading status in the surrounding wrapper. If the JavaScript fails to load or initialize, the placeholder remains visible.

## Adding affix text aside the field

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->prefix('From')
    ->suffix('inclusive')
```

<UtilityInjection set="formFields">As well as allowing static values, the `prefix()` and `suffix()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

### Using icons as affixes

You may place an [icon](../styling/icons) before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Forms\Components\DateRangePicker;
use Filament\Support\Icons\Heroicon;

DateRangePicker::make('period')
    ->prefixIcon(Heroicon::CalendarDateRange)
```

<UtilityInjection set="formFields">As well as allowing static values, the `prefixIcon()` and `suffixIcon()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/date-range-picker/prefix-icon" alt="Date range picker with prefix icon" />

## Making the field read-only

Not to be confused with [disabling the field](overview#disabling-a-field), you may make the field "read-only" using the `readOnly()` method:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->readOnly()
```

A read-only date range picker remains focusable, but cannot open the calendar or clear the value.

There are a few differences, compared to [`disabled()`](overview#disabling-a-field):

- When using `readOnly()`, the field will still be sent to the server when the form is submitted. It can be mutated with the browser console, or via JavaScript. You can use [`saved(false)`](overview#preventing-a-field-from-being-saved) to prevent this.
- There are no styling changes, such as less opacity, when using `readOnly()`.
- The field is still focusable when using `readOnly()`.

Optionally, you may pass a boolean value to control if the field should be read-only or not:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->readOnly(FeatureFlag::active())
```

<UtilityInjection set="formFields">As well as allowing a static value, the `readOnly()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Date range picker validation

As well as all rules listed on the [validation](validation) page, there are additional rules that are specific to date range pickers.

Both dates must be valid, and the end date must not be before the start date. Otherwise, Laravel's `date` validation message is used.

### Max date / min date validation

You may restrict the earliest and latest dates that can be selected with the picker. The `minDate()` and `maxDate()` methods accept a `DateTime` instance (e.g. `Carbon`), or a string:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('period')
    ->minDate(now()->subYear())
    ->maxDate(now())
```

The time portion of each limit is ignored, so the entire minimum and maximum calendar dates are allowed. Dates outside the limits are disabled in the calendar, and a range that starts before the minimum date or ends after the maximum date fails validation with Laravel's `after_or_equal` or `before_or_equal` message.

<UtilityInjection set="formFields">As well as allowing static values, the `minDate()` and `maxDate()` methods also accept functions to dynamically calculate them. If the functions return `null`, the validation rule is not applied. You can inject various utilities into the functions as parameters.</UtilityInjection>

### Limiting the length of the range

You may restrict the number of days that can be selected using the `maxDays()` method. Both the start and the end date are counted, so a range from Monday to Sunday is 7 days long:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('stay')
    ->maxDays(14)
```

Once the start date is selected, the calendar disables the dates that would make the range too long. A longer range fails validation with a message such as "The stay field must not be longer than 14 days.". You may customize this message using the `max_days` key of the `validationMessages()` method:

```php
use Filament\Forms\Components\DateRangePicker;

DateRangePicker::make('stay')
    ->maxDays(14)
    ->validationMessages([
        'max_days' => 'Stays are limited to two weeks.',
    ])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `maxDays()` method also accepts a function to dynamically calculate it. If the function returns `null`, the validation rule is not applied. You can inject various utilities into the function as parameters.</UtilityInjection>
