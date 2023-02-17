---
title: Progress column
---

Progress columns render a progress bar based on the values passed.

```php
use Filament\Tables\Columns\ProgressColumn;

ProgressColumn::make('progress')
```

## Dynamic progress calculation

If you wish to calculate the progress dynamically, provide a `Closure` to the `ProgressColumn::progress()` method.

```php
use Filament\Tables\Columns\ProgressColumn;

ProgressColumn::make('progress')
    ->progress(function ($record) {
        return ($record->rows_complete / $record->total_rows) * 100;
    })
```

## Colors

By default, the progress bar will be the same as your `primary` color. If you wish to change this, provide a new string to `ProgressBar::color()`.

```php
use Filament\Tables\Columns\ProgressColumn;

ProgressColumn::make('progress')
    ->color('warning')
```

With a [custom filament theme](https://filamentphp.com/docs/2.x/admin/appearance#building-themes) you can add `'./app/Filament/Resources/*.php'` to the `content` section in `tailwind.config.js` so colors won't get purged and create [gradient colors](https://tailwindcss.com/docs/gradient-color-stops#middle-color) like

```php
use Filament\Tables\Columns\ProgressColumn;

ProgressColumn::make('progress')
    ->color('bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500'),
```

## Dynamic color calculation

If you wish to calculate the color dynamically, provide a `Closure` to the `ProgressColumn::color()` method.

```php
use Filament\Tables\Columns\ProgressColumn;

ProgressColumn::make('progress')
    ->color(function ($record){
        return $record->progress > 50 ? 'primary' : 'success';
    })
```