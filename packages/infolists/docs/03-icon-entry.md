---
title: Icon entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Icon entries render an [icon](../styling/icons) representing the state of the entry:

```php
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Icons\Heroicon;

IconEntry::make('status')
    ->icon(fn (string $state): Heroicon => match ($state) {
        'draft' => Heroicon::OutlinedPencil,
        'reviewing' => Heroicon::OutlinedClock,
        'published' => Heroicon::OutlinedCheckCircle,
    })
```

<UtilityInjection set="infolistEntries" version="4.x">The `icon()` method can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/icon/simple" alt="Icon entry" version="4.x" />

## Customizing the color

You may change the [color](../styling/colors) of the icon, using the `color()` method:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('status')
    ->color('success')
```

By passing a function to `color()`, you can customize the color based on the state of the entry:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('status')
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'info',
        'reviewing' => 'warning',
        'published' => 'success',
        default => 'gray',
    })
```

<UtilityInjection set="infolistEntries" version="4.x">The `color()` method can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/icon/color" alt="Icon entry with color" version="4.x" />

## Customizing the size

The default icon size is `IconSize::Large`, but you may customize the size to be either `IconSize::ExtraSmall`, `IconSize::Small`, `IconSize::Medium`, `IconSize::ExtraLarge` or `IconSize::TwoExtraLarge`:

```php
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\IconSize;

IconEntry::make('status')
    ->size(IconSize::Medium)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `size()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/icon/medium" alt="Medium-sized icon entry" version="4.x" />

## Handling booleans

Icon entries can display a check or "X" icon based on the state of the entry, either true or false, using the `boolean()` method:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
```

> If this attribute in the model class is already cast as a `bool` or `boolean`, Filament is able to detect this, and you do not need to use `boolean()` manually.

<AutoScreenshot name="infolists/entries/icon/boolean" alt="Icon entry to display a boolean" version="4.x" />

Optionally, you may pass a boolean value to control if the icon should be boolean or not:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `boolean()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing the boolean icons

You may customize the [icon](../styling/icons) representing each state:

```php
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Icons\Heroicon;

IconEntry::make('is_featured')
    ->boolean()
    ->trueIcon(Heroicon::OutlinedCheckBadge)
    ->falseIcon(Heroicon::OutlinedXMark)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `trueIcon()` and `falseIcon()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/icon/boolean-icon" alt="Icon entry to display a boolean with custom icons" version="4.x" />

### Customizing the boolean colors

You may customize the icon [color](../styling/colors) representing each state:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
    ->trueColor('info')
    ->falseColor('warning')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `trueColor()` and `falseColor()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/icon/boolean-color" alt="Icon entry to display a boolean with custom colors" version="4.x" />
