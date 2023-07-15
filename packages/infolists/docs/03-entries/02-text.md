---
title: Text entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Text entries display simple text:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
```

<AutoScreenshot name="infolists/entries/text/simple" alt="Text entry" version="3.x" />

## Displaying as a "badge"

By default, text is quite plain and has no background color. You can make it appear as a "badge" instead using the `badge()` method. A great use case for this is with statuses, where may want to display a badge with a [color](#customizing-the-color) that matches the status:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        'rejected' => 'danger',
    })
```

<AutoScreenshot name="infolists/entries/text/badge" alt="Text entry as badge" version="3.x" />

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

You may `limit()` the length of the entry's value:

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

You may limit the number of `words()` displayed in the entry:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->words(10)
```

## Listing multiple values

By default, if there are multiple values inside your text entry, they will be comma-separated. You may use the `listWithLineBreaks()` method to display them on new lines instead:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
```

<AutoScreenshot name="infolists/entries/text/list" alt="Text entry with multiple values" version="3.x" />

### Adding bullet points to the list

You may add a bullet point to each list item using the `bulleted()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->bulleted()
```

<AutoScreenshot name="infolists/entries/text/bullet-list" alt="Text entry with multiple values and bullet points" version="3.x" />

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

You may instead pass a custom formatting callback to `formatStateUsing()`, which accepts the `$state` of the entry, and optionally its `$record`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

## Adding a placeholder if the entry is empty

Sometimes you may want to display a placeholder if the entry's value is empty:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('updated_at')
    ->placeholder('Never')
```

## Customizing the color

You may set a color for the text, either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->color('primary')
```

<AutoScreenshot name="infolists/entries/text/color" alt="Text entry in the primary color" version="3.x" />

## Adding an icon

Text entries may also have an [icon](https://blade-ui-kit.com/blade-icons?set=1#search):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
```

<AutoScreenshot name="infolists/entries/text/icon" alt="Text entry with icon" version="3.x" />

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
    ->iconPosition('after') // `before` or `after`
```

<AutoScreenshot name="infolists/entries/text/icon-after" alt="Text entry with icon after" version="3.x" />

## Customizing the text size

You may make the text larger using `size('lg')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->size('lg')
```

<AutoScreenshot name="infolists/entries/text/large" alt="Text entry in a large font size" version="3.x" />

## Customizing the font weight

Text entries have regular font weight by default but you may change this to any of the the following options: `thin`, `extralight`, `light`, `medium`, `semibold`, `bold`, `extrabold` or `black`.

For instance, you may make the font bold using `weight('bold')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->weight('bold')
```

<AutoScreenshot name="infolists/entries/text/bolt" alt="Text entry in a bold font" version="3.x" />

## Customizing the font family

You can change the text font family to any of the following options: `sans`, `serif` or `mono`.

For instance, you may make the font monospaced using `fontFamily('mono')`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('apiKey')
    ->label('API key')
    ->fontFamily('mono')
```

<AutoScreenshot name="infolists/entries/text/mono" alt="Text entry in a monospaced font" version="3.x" />

## Allowing the text to be copied to the clipboard

You may make the text copyable, such that clicking on the entry copies the text to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('apiKey')
    ->label('API key')
    ->copyable()
    ->copyMessage('Copied!')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="infolists/entries/text/copyable" alt="Text entry with a button to copy it" version="3.x" />
