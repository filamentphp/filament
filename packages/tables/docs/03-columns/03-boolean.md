---
title: Boolean column
---

Boolean columns display a check or cross icon based on the contents of the database column, either true or false:

```php
use Filament\Tables\Columns\BooleanColumn;

BooleanColumn::make('is_featured')
```

## Customizing the icons

You may customize the icon representing each state. Icons are the name of a Blade component present. By default, [Heroicons](https://heroicons.com) are installed:

```php
use Filament\Tables\Columns\BooleanColumn;

BooleanColumn::make('is_featured')
    ->trueIcon('heroicon-o-badge-check')
    ->falseIcon('heroicon-o-x-circle')
```

## Customizing the colors

You may customize the icon color representing each state. These may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\BooleanColumn;

BooleanColumn::make('is_featured')
    ->trueColor('primary')
    ->falseColor('warning')
```
