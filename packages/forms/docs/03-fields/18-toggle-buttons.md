---
title: Toggle buttons
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The toggle buttons input provides a group of buttons for selecting a single value, or multiple values, from a list of predefined options:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
```

<AutoScreenshot name="forms/fields/toggle-buttons/simple" alt="Toggle buttons" version="3.x" />

## Changing the color of option buttons

You can change the color of the option buttons using the `colors()` method. Each key in the array should correspond to an option value, and the value may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
    ->colors([
        'draft' => 'info',
        'scheduled' => 'warning',
        'published' => 'success',
    ])
])
```

If you are using an enum for the options, you can use the [`HasColor` interface](../../support/enums#enum-colors) to define icons instead.

<AutoScreenshot name="forms/fields/toggle-buttons/colors" alt="Toggle buttons with different colors" version="3.x" />

## Adding icons to option buttons

You can add [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to the option buttons using the `icons()` method. Each key in the array should correspond to an option value, and the value may be any valid [Blade icon](https://blade-ui-kit.com/blade-icons?set=1#search):

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
    ->icons([
        'draft' => 'heroicon-o-pencil',
        'scheduled' => 'heroicon-o-clock',
        'published' => 'heroicon-o-check-circle',
    ])
])
```

If you are using an enum for the options, you can use the [`HasIcon` interface](../../support/enums#enum-icons) to define icons instead.

<AutoScreenshot name="forms/fields/toggle-buttons/icons" alt="Toggle buttons with icons" version="3.x" />

## Boolean options

If you want a simple boolean toggle button group, with "Yes" and "No" options, you can use the `boolean()` method:

```php
ToggleButtons::make('feedback')
    ->label('Like this post??')
    ->boolean()
```

The options will have [colors](#changing-the-color-of-option-buttons) and [icons](#adding-icons-to-option-buttons) set up automatically, but you can override these with `colors()` or `icons()`.

<AutoScreenshot name="forms/fields/toggle-buttons/boolean" alt="Boolean toggle buttons" version="3.x" />

## Positioning the options inline with each other

You may wish to display the options `inline()` with each other:

```php
ToggleButtons::make('feedback')
    ->label('Like this post??')
    ->boolean()
    ->inline()
```

<AutoScreenshot name="forms/fields/toggle-buttons/inline" alt="Inline toggle buttons" version="3.x" />

## Grouping option buttons

You may wish to group option buttons together so they are more compact, using the `grouped()` method. This also makes them appear horizontally inline with each other:

```php
ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
    ->grouped()
```

<AutoScreenshot name="forms/fields/toggle-buttons/grouped" alt="Grouped toggle buttons" version="3.x" />

## Selecting multiple buttons

The `multiple()` method on the `ToggleButtons` component allows you to select multiple values from the list of options:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('technologies')
    ->multiple()
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

<AutoScreenshot name="forms/fields/toggle-buttons/multiple" alt="Multiple toggle buttons selected" version="3.x" />

These options are returned in JSON format. If you're saving them using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $casts = [
        'technologies' => 'array',
    ];

    // ...
}
```

## Splitting options into columns

You may split options into columns by using the `columns()` method:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('technologies')
    ->options([
        // ...
    ])
    ->columns(2)
```

<AutoScreenshot name="forms/fields/toggle-buttons/columns" alt="Toggle buttons with 2 columns" version="3.x" />

This method accepts the same options as the `columns()` method of the [grid](layout/grid). This allows you to responsively customize the number of columns at various breakpoints.

### Setting the grid direction

By default, when you arrange buttons into columns, they will be listed in order vertically. If you'd like to list them horizontally, you may use the `gridDirection('row')` method:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('technologies')
    ->options([
        // ...
    ])
    ->columns(2)
    ->gridDirection('row')
```

<AutoScreenshot name="forms/fields/toggle-buttons/rows" alt="Toggle buttons with 2 rows" version="3.x" />

## Disabling specific options

You can disable specific options using the `disableOptionWhen()` method. It accepts a closure, in which you can check if the option with a specific `$value` should be disabled:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'published')
```

<AutoScreenshot name="forms/fields/toggle-buttons/disabled-option" alt="Toggle buttons with disabled option" version="3.x" />

If you want to retrieve the options that have not been disabled, e.g. for validation purposes, you can do so using `getEnabledOptions()`:

```php
use Filament\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'published')
    ->in(fn (ToggleButtons $component): array => array_keys($component->getEnabledOptions()))
```
