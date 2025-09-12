---
title: Text input column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

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

## Affixes

You can add prefixes and suffixes to the text input column to provide additional context or functionality:

### Prefix and suffix labels

Add text labels before or after the input:

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('price')
    ->prefix('$')
    ->suffix('.00')

TextInputColumn::make('quantity')
    ->suffix(' MBq')
```

### Prefix and suffix icons

Add icons before or after the input:

```php
use Filament\Tables\Columns\TextInputColumn;
use Filament\Support\Enums\IconSize;

TextInputColumn::make('email')
    ->prefixIcon('heroicon-m-envelope')
    ->suffixIcon('heroicon-m-check-circle')
    ->suffixIconColor('success')
```

### Inline affixes

By default, affixes are displayed outside the input field. You can make them inline:

```php
TextInputColumn::make('price')
    ->prefix('$', isInline: true)
    ->suffix('.00', isInline: true)
```

### Affix actions

You can add interactive buttons as affixes:

```php
use Filament\Actions\Action;
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('email')
    ->suffixAction(
        Action::make('send_email')
            ->icon('heroicon-m-paper-airplane')
            ->action(fn ($record) => // Send email logic)
    )
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