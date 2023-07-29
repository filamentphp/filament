---
title: Icon entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Icon entries render an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) representing their contents:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('status')
    ->icon(fn (string $state): string => match ($state) {
        'draft' => 'heroicon-o-pencil',
        'reviewing' => 'heroicon-o-clock',
        'published' => 'heroicon-o-check-circle',
    })
```

In the function, `$state` is the value of the entry, and `$record` can be used to access the underlying Eloquent record.

<AutoScreenshot name="infolists/entries/icon/simple" alt="Icon entry" version="3.x" />

## Customizing the color

Icon entries may also have a set of icon colors, using the same syntax. They may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

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

In the function, `$state` is the value of the entry, and `$record` can be used to access the underlying Eloquent record.

<AutoScreenshot name="infolists/entries/icon/color" alt="Icon entry with color" version="3.x" />

## Customizing the size

The default icon size is `IconEntrySize::Large`, but you may customize the size to be either `IconEntrySize::ExtraSmall`, `IconEntrySize::Small`, `IconEntrySize::Medium` or `IconEntrySize::ExtraLarge`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('status')
    ->size(IconEntry\IconEntrySize::Medium)
```

<AutoScreenshot name="infolists/entries/icon/medium" alt="Medium-sized icon entry" version="3.x" />

## Handling booleans

Icon entries can display a check or cross icon based on the contents of the database entry, either true or false, using the `boolean()` method:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
```

<AutoScreenshot name="infolists/entries/icon/boolean" alt="Icon entry to display a boolean" version="3.x" />

### Customizing the boolean icons

You may customize the icon representing each state. Icons are the name of a Blade component present. By default, [Heroicons](https://heroicons.com) are installed:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
    ->trueIcon('heroicon-o-check-badge')
    ->falseIcon('heroicon-o-x-mark')
```

<AutoScreenshot name="infolists/entries/icon/boolean-icon" alt="Icon entry to display a boolean with custom icons" version="3.x" />

### Customizing the boolean colors

You may customize the icon color representing each state. These may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
    ->trueColor('info')
    ->falseColor('warning')
```

<AutoScreenshot name="infolists/entries/icon/boolean-color" alt="Icon entry to display a boolean with custom colors" version="3.x" />
