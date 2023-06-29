---
title: Icon entry
---

## Overview

Icon entries render an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) representing their contents:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->icons([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
```

You may also pass a callback to activate an option, accepting the entry's `$state` and `$record`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->icons([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => fn ($state, $record): bool => $record->status === 2,
        'heroicon-o-clock' => fn ($state): bool => $state === 'reviewing',
        'heroicon-o-check-circle' => fn ($state): bool => $state === 'published',
    ])
```

## Customizing the color

Icon entries may also have a set of icon colors, using the same syntax. They may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->icons([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
    ->colors([
        'gray',
        'danger' => 'draft',
        'warning' => 'reviewing',
        'success' => 'published',
    ])
```

## Customizing the size

The default icon size is `lg`, but you may customize the size to be either `xs`, `sm`, `md`, `lg` or `xl`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->icons([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
    ->size('md')
```

## Handling booleans

Icon entries can display a check or cross icon based on the contents of the database entry, either true or false, using the `boolean()` method:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
```

### Customizing the boolean icons

You may customize the icon representing each state. Icons are the name of a Blade component present. By default, [Heroicons v1](https://v1.heroicons.com) are installed:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
    ->trueIcon('heroicon-o-check-badge')
    ->falseIcon('heroicon-o-x-circle')
```

### Customizing the boolean colors

You may customize the icon color representing each state. These may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Infolists\Components\IconEntry;

IconEntry::make('is_featured')
    ->boolean()
    ->trueColor('primary')
    ->falseColor('warning')
```
