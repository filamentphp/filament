---
title: Text column
---

Text columns display simple text from your database:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
```

## Displaying a description

Descriptions may be used to easily render additional text above or below the column contents.

You can display a description below the contents of a text column using the `description()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->description(fn (Post $record): string => $record->description)
```

By default, the description is displayed below the main text, but you can move it above using the second parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->description(fn (Post $record): string => $record->description, position: 'above')
```

## Date formatting

You may use the `date()` and `dateTime()` methods to format the column's state using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')->dateTime()
```

You may use the `since()` method to format the column's state using [Carbon's `diffForHumans()`](https://carbon.nesbot.com/docs/#api-humandiff):

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')->since()
```

## Currency formatting

The `money()` method allows you to easily format monetary values, in any currency. This functionality uses [`akaunting/laravel-money`](https://github.com/akaunting/laravel-money) internally:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')->money('eur')
```

## Limiting text length

You may `limit()` the length of the cell's value:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')->limit(50)
```

You may also reuse the value that is being passed to `limit()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->limit(50)
    ->tooltip(function (TextColumn $column): ?string {
        $state = $column->getState();

        if (strlen($state) <= $column->getLimit()) {
            return null;
        }

        // Only render the tooltip if the column contents exceeds the length limit.
        return $state;
    })
```

## Wrapping content

If you'd like your column's content to wrap if it's too long, you may use the `wrap()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')->wrap()
```

## Rendering HTML

If your column value is HTML, you may render it using `html()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')->html()
```

## Enum formatting

You may also transform a set of known cell values using the `enum()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')->enum([
    'draft' => 'Draft',
    'reviewing' => 'Reviewing',
    'published' => 'Published',
])
```

## Custom formatting

You may instead pass a custom formatting callback to `formatStateUsing()`, which accepts the `$state` of the cell, and optionally its `$record`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

## Customizing the color

You may set a color for the text, either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->color('primary')
```

## Adding an icon

Text columns may also have an icon:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->icon('heroicon-s-mail')
```

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->icon('heroicon-s-mail')
    ->iconPosition('after') // `before` or `after`
```

## Customizing the text size

You may make the text smaller using `size('sm')`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->size('sm')
```

Or you can make it larger using `size('lg')`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->size('lg')
```

## Customizing the font weight

Text columns have regular font weight by default but you may change this to any of the the following options: `thin`, `extralight`, `light`, `medium`, `semibold`, `bold`, `extrabold` or `black`.

For instance, you may make the font bold using `weight('bold')`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->weight('bold')
```
