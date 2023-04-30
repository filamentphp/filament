---
title: Icon column
---

Icon columns render a Blade icon component representing their contents:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->options([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
```

You may also pass a callback to activate an option, accepting the cell's `$state` and `$record`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->options([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => fn ($state, $record): bool => $record->status === 2,
        'heroicon-o-clock' => fn ($state): bool => $state === 'reviewing',
        'heroicon-o-check-circle' => fn ($state): bool => $state === 'published',
    ])
```

## Customizing the color

Icon columns may also have a set of icon colors, using the same syntax. They may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->options([
        'heroicon-o-x-circle',
        'heroicon-o-pencil' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
    ->colors([
        'secondary',
        'danger' => 'draft',
        'warning' => 'reviewing',
        'success' => 'published',
    ])
```

## Customizing the size

The default icon size is `lg`, but you may customize the size to be either `xs`, `sm`, `md`, `lg` or `xl`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->options([
        'heroicon-s-x-circle',
        'heroicon-s-pencil' => 'draft',
        'heroicon-s-clock' => 'reviewing',
        'heroicon-s-check-circle' => 'published',
    ])
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
    ->trueIcon('heroicon-o-badge-check')
    ->falseIcon('heroicon-o-x-circle')
```

### Customizing the boolean colors

You may customize the icon color representing each state. These may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()
    ->trueColor('primary')
    ->falseColor('warning')
```
