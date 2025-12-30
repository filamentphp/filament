---
title: Text input column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The text input column allows you to render a text input inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('email')
```

<AutoScreenshot name="tables/columns/text-input/simple" alt="Text input column" version="4.x" />

## Validation

You can validate the input by passing any [Laravel validation rules](https://laravel.com/docs/validation#available-validation-rules) in an array:

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('name')
    ->rules(['required', 'max:255'])
```

## Customizing the HTML input type

You may use the `type()` method to pass a custom [HTML input type](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#input_types):

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('background_color')->type('color')
```

## Lifecycle hooks

Hooks may be used to execute code at various points within the input's lifecycle:

```php
TextInputColumn::make()
    ->beforeStateUpdated(function ($record, $state) {
        // Runs before the state is saved to the database.
    })
    ->afterStateUpdated(function ($record, $state) {
        // Runs after the state is saved to the database.
    })
```

## Adding affix text aside the field

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('domain')
    ->prefix('https://')
    ->suffix('.com')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `prefix()` and `suffix()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

### Using icons as affixes

You may place an [icon](../../styling/icons) before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Tables\Columns\TextInputColumn;
use Filament\Support\Icons\Heroicon;

TextInputColumn::make('domain')
    ->prefixIcon(Heroicon::GlobeAlt)
    ->suffixIcon(Heroicon::CheckCircle)
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `prefixIcon()` and `suffixIcon()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

#### Setting the affix icon's color

Affix icons are gray by default, but you may set a different color using the `prefixIconColor()` and `suffixIconColor()` methods:

```php
use Filament\Tables\Columns\TextInputColumn;
use Filament\Support\Icons\Heroicon;

TextInputColumn::make('status')
    ->suffixIcon(Heroicon::CheckCircle)
    ->suffixIconColor('success')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `prefixIconColor()` and `suffixIconColor()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>
