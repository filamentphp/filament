---
title: Text entry
---

## Overview

Text entries display simple text:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
```

## Displaying as a "badge"

By default, text is quite plain and has no background color. You can make it appear as a "badge" instead using the `badge()` method. A great use case for this is with statuses, where may want to display a badge with a [color](#customizing-the-color) that matches the status:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->badge()
    ->color(fn (string $status): string => match ($status) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        'rejected' => 'danger',
    })
```

You may add other things to the badge, like an [icon](#adding-an-icon).

## Date formatting

You may use the `date()` and `dateTime()` methods to format the entry's state using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->dateTime()
```

You may use the `since()` method to format the entry's state using [Carbon's `diffForHumans()`](https://carbon.nesbot.com/docs/#api-humandiff):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->since()
```

## Number formatting

The `numeric()` method allows you to format a entry as a number, using PHP's `number_format()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('stock')
    ->numeric(
        decimalPlaces: 0,
        decimalSeparator: '.',
        thousandsSeparator: ',',
    )
```

## Currency formatting

The `money()` method allows you to easily format monetary values, in any currency:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('eur')
```

## Limiting text length

You may `limit()` the length of the cell's value:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->limit(50)
```

You may also reuse the value that is being passed to `limit()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->limit(50)
    ->tooltip(function (TextEntry $entry): ?string {
        $state = $entry->getState();

        if (strlen($state) <= $entry->getCharacterLimit()) {
            return null;
        }

        // Only render the tooltip if the entry contents exceeds the length limit.
        return $state;
    })
```

## Limiting word count

You may limit the number of `words()` displayed in the cell:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->words(10)
```

## Wrapping content

If you'd like your entry's content to wrap if it's too long, you may use the `wrap()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->wrap()
```

## Listing multiple values

By default, if there are multiple values inside your text entry, they will be comma-separated. You may use the `listWithLineBreaks()` method to display them on new lines instead:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
```

### Adding bullet points to the list

You may add a bullet point to each list item using the `bulleted()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->bulleted()
```

### Limiting the number of values in the list

You can limit the number of values in the list using the `limitList()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
```

### Using a list separator

If you want to "explode" a text string from your model into multiple list items, you can do so with the `separator()` method. This is useful for displaying comma-separated tags [as badges](#displaying-as-a-badge), for example:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('tags')
    ->badge()
    ->separator(',')
```

## Rendering HTML

If your entry value is HTML, you may render it using `html()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->html()
```

### Rendering Markdown as HTML

If your entry value is Markdown, you may render it using `markdown()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->markdown()
```

## Custom formatting

You may instead pass a custom formatting callback to `formatStateUsing()`, which accepts the `$state` of the cell, and optionally its `$record`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

## Adding a placeholder if the cell is empty

Sometimes you may want to display a placeholder if the cell's value is empty:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('updated_at')
    ->placeholder('Never')
```

## Customizing the color

You may set a color for the text, either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->color('primary')
```

## Adding an icon

Text entries may also have an [icon](https://blade-ui-kit.com/blade-icons?set=1#search):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
```

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
    ->iconPosition('after') // `before` or `after`
```

## Customizing the text size

You may make the text smaller using `size('sm')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->size('sm')
```

Or you can make it larger using `size('lg')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->size('lg')
```

## Customizing the font weight

Text entries have regular font weight by default but you may change this to any of the the following options: `thin`, `extralight`, `light`, `medium`, `semibold`, `bold`, `extrabold` or `black`.

For instance, you may make the font bold using `weight('bold')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->weight('bold')
```

## Customizing the font family

You can change the text font family to any of the following options: `sans`, `serif` or `mono`.

For instance, you may make the font mono using `fontFamily('mono')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('text')
    ->fontFamily('mono')
```

## Allowing the text to be copied to the clipboard

You may make the text copyable, such that clicking on the cell copies the text to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->copyable()
    ->copyMessage('Email address copied')
    ->copyMessageDuration(1500)
```

> Filament uses tooltips to display the copy message in the admin panel. If you want to use the copyable feature outside of the admin panel, make sure you have [`@ryangjchandler/alpine-tooltip` installed](https://github.com/ryangjchandler/alpine-tooltip#installation) in your app.
