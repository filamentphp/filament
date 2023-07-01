---
title: Icon column
---

## Overview

Icon columns render an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) representing their contents:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('status')
    ->icon(fn (string $state): string => match ($state) {
        'draft' => 'heroicon-o-pencil',
        'reviewing' => 'heroicon-o-clock',
        'published' => 'heroicon-o-check-circle',
    })
```

In the function, `$state` is the value of the column, and `$record` can be used to access the underlying Eloquent record.

## Customizing the color

Icon columns may also have a set of icon colors, using the same syntax. They may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('status')
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'danger',
        'reviewing' => 'warning',
        'published' => 'success',
        default => 'gray',
    })
```

In the function, `$state` is the value of the column, and `$record` can be used to access the underlying Eloquent record.

## Customizing the size

The default icon size is `lg`, but you may customize the size to be either `xs`, `sm`, `md`, `lg` or `xl`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('status')
    ->size('md')
```

## Handling booleans

Icon columns can display a check or cross icon based on the contents of the database column, either true or false, using the `boolean()` method:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
```

### Customizing the boolean icons

You may customize the icon representing each state. Icons are the name of a Blade component present. By default, [Heroicons v1](https://v1.heroicons.com) are installed:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
    ->trueIcon('heroicon-o-check-badge')
    ->falseIcon('heroicon-o-x-circle')
```

### Customizing the boolean colors

You may customize the icon color representing each state. These may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
    ->trueColor('primary')
    ->falseColor('warning')
```
