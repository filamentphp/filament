---
title: Text input column
---
import Aside from "@components/Aside.astro"
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

You can validate the input by passing any Laravel validation rules in an array:

```php
use Filament\Tables\Columns\TextInputColumn;

TextInputColumn::make('name')
    ->rules(['required', 'max:255'])
```

## Customizing the HTML input type

You may use the `type()` method to pass a custom HTML input type:

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

## Prefix and Suffix Support

The text input column supports prefix and suffix elements including labels, icons, and actions:

### Prefix and Suffix Labels

Add text labels to provide context for your input fields:

```php
TextInputColumn::make('weight')
    ->prefix('Weight: ')
    ->suffix(' kg')
    ->numeric()

TextInputColumn::make('email')
    ->prefix('@')
    ->suffix('@company.com')
```

### Icons

Include icons with customizable colors:

```php
TextInputColumn::make('phone')
    ->prefixIcon('heroicon-o-phone')
    ->prefixIconColor('success')
    ->suffixIcon('heroicon-o-check-circle')
    ->suffixIconColor('success')

TextInputColumn::make('amount')
    ->prefixIcon('heroicon-o-currency-dollar')
    ->prefixIconColor(['primary' => 500])
    ->suffixIcon('heroicon-o-banknotes')
    ->suffixIconColor(['success' => 600])
```

### Actions

Add interactive action buttons:

```php
use Filament\Actions\Action;

TextInputColumn::make('quantity')
    ->prefixAction(
        Action::make('decrease')
            ->icon('heroicon-o-minus')
            ->action(fn ($record) => $record->decrement('quantity'))
    )
    ->suffixAction(
        Action::make('increase')
            ->icon('heroicon-o-plus')
            ->action(fn ($record) => $record->increment('quantity'))
    )
```

### Inline Layout

Control whether affixes are displayed inline with the input:

```php
TextInputColumn::make('price')
    ->prefix('$')
    ->inlinePrefix() // Display prefix inline
    ->suffix('USD')
    ->inlineSuffix(false) // Display suffix as separate element
```

## Advanced Examples

### Currency Input with Actions

```php
TextInputColumn::make('price')
    ->prefix('$')
    ->prefixIcon('heroicon-o-currency-dollar')
    ->prefixIconColor('success')
    ->suffixAction(
        Action::make('calculate_tax')
            ->icon('heroicon-o-calculator')
            ->action(fn ($record, $data) => $record->update(['price' => $data * 1.21]))
    )
    ->numeric()
    ->step(0.01)
```

### Search Input with Clear Action

```php
TextInputColumn::make('search')
    ->prefixIcon('heroicon-o-magnifying-glass')
    ->suffixAction(
        Action::make('clear')
            ->icon('heroicon-o-x-mark')
            ->action(fn ($record) => $record->update(['search' => '']))
    )
    ->placeholder('Search...')
```

### Status Input with Visual Indicators

```php
TextInputColumn::make('status')
    ->prefixIcon(fn ($state) => match($state) {
        'active' => 'heroicon-o-check-circle',
        'inactive' => 'heroicon-o-x-circle',
        default => 'heroicon-o-question-mark-circle'
    })
    ->prefixIconColor(fn ($state) => match($state) {
        'active' => 'success',
        'inactive' => 'danger',
        default => 'gray'
    })
    ->suffixLabel(fn ($state) => match($state) {
        'active' => '✓',
        'inactive' => '✗',
        default => '?'
    })
```

## Available Methods

### Prefix Methods
- `prefix(string|Htmlable|Closure|null $label, bool|Closure $isInline = false)`
- `prefixAction(Action|Closure $action, bool|Closure $isInline = false)`
- `prefixActions(array $actions, bool|Closure $isInline = false)`
- `prefixIcon(string|BackedEnum|Closure|null $icon, bool|Closure $isInline = false)`
- `prefixIconColor(string|array|Closure|null $color = null)`
- `inlinePrefix(bool|Closure $isInline = true)`

### Suffix Methods
- `suffix(string|Htmlable|Closure|null $label, bool|Closure $isInline = false)`
- `suffixAction(Action|Closure $action, bool|Closure $isInline = false)`
- `suffixActions(array $actions, bool|Closure $isInline = false)`
- `suffixIcon(string|BackedEnum|Closure|null $icon, bool|Closure $isInline = false)`
- `suffixIconColor(string|array|Closure|null $color = null)`
- `inlineSuffix(bool|Closure $isInline = true)`

### Legacy Methods
- `postfix(string|Htmlable|Closure|null $label, bool|Closure $isInline = false)` - Alias for `suffix()`
