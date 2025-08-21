---
title: Icon column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import Aside from "@components/Aside.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Icon columns render an [icon](../../styling/icons) representing the state of the column:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Icons\Heroicon;

IconColumn::make('status')
    ->icon(fn (string $state): Heroicon => match ($state) {
        'draft' => Heroicon::OutlinedPencil,
        'reviewing' => Heroicon::OutlinedClock,
        'published' => Heroicon::OutlinedCheckCircle,
    })
```

<UtilityInjection set="tableColumns" version="4.x">The `icon()` method can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/icon/simple" alt="Icon column" version="4.x" />

## Customizing the color

You may change the [color](../../styling/colors) of the icon, using the `color()` method:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('status')
    ->color('success')
```

By passing a function to `color()`, you can customize the color based on the state of the column:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('status')
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'info',
        'reviewing' => 'warning',
        'published' => 'success',
        default => 'gray',
    })
```

<UtilityInjection set="tableColumns" version="4.x">The `color()` method can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/icon/color" alt="Icon column with color" version="4.x" />

## Customizing the size

The default icon size is `IconSize::Large`, but you may customize the size to be either `IconSize::ExtraSmall`, `IconSize::Small`, `IconSize::Medium`, `IconSize::ExtraLarge` or `IconSize::TwoExtraLarge`:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\IconSize;

IconColumn::make('status')
    ->size(IconSize::Medium)
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `size()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/icon/medium" alt="Medium-sized icon column" version="4.x" />

## Handling booleans

Icon columns can display a check or "X" icon based on the state of the column, either true or false, using the `boolean()` method:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
```

> If this attribute in the model class is already cast as a `bool` or `boolean`, Filament is able to detect this, and you do not need to use `boolean()` manually.

<AutoScreenshot name="tables/columns/icon/boolean" alt="Icon column to display a boolean" version="4.x" />

Optionally, you may pass a boolean value to control if the icon should be boolean or not:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean(FeatureFlag::active())
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `boolean()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing the boolean icons

You may customize the [icon](../../styling/icons) representing each state:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Icons\Heroicon;

IconColumn::make('is_featured')
    ->boolean()
    ->trueIcon(Heroicon::OutlinedCheckBadge)
    ->falseIcon(Heroicon::OutlinedXMark)
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `trueIcon()` and `falseIcon()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/icon/boolean-icon" alt="Icon column to display a boolean with custom icons" version="4.x" />

### Customizing the boolean colors

You may customize the icon [color](../../styling/colors) representing each state:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
    ->trueColor('info')
    ->falseColor('warning')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `trueColor()` and `falseColor()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/icon/boolean-color" alt="Icon column to display a boolean with custom colors" version="4.x" />

## Wrapping multiple icons

When displaying multiple icons, they can be set to wrap if they can't fit on one line, using `wrap()`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('icon')
    ->wrap()
```

<Aside variant="tip">
    The "width" for wrapping is affected by the column label, so you may need to use a shorter or hidden label to wrap more tightly.
</Aside>
