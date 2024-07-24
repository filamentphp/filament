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

Additionally, you can use the `dateTooltip()`, `dateTimeTooltip()` or `timeTooltip()` method to display a formatted date in a tooltip, often to provide extra information:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->since()
    ->dateTimeTooltip()
```

## Number formatting

The `numeric()` method allows you to format an entry as a number:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('stock')
    ->numeric()
```

If you would like to customize the number of decimal places used to format the number with, you can use the `decimalPlaces` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('stock')
    ->numeric(decimalPlaces: 0)
```

By default, your app's locale will be used to format the number suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('stock')
    ->numeric(locale: 'nl')
```

Alternatively, you can set the default locale used across your app using the `Infolist::$defaultNumberLocale` method in the `boot()` method of a service provider:

```php
use Filament\Infolists\Infolist;

Infolist::$defaultNumberLocale = 'nl';
```

## Currency formatting

The `money()` method allows you to easily format monetary values, in any currency:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR')
```

There is also a `divideBy` argument for `money()` that allows you to divide the original value by a number before formatting it. This could be useful if your database stores the price in cents, for example:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR', divideBy: 100)
```

By default, your app's locale will be used to format the money suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR', locale: 'nl')
```

Alternatively, you can set the default locale used across your app using the `Infolist::$defaultNumberLocale` method in the `boot()` method of a service provider:

```php
use Filament\Infolists\Infolist;

Infolist::$defaultNumberLocale = 'nl';
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
    ->tooltip(function (TextEntry $component): ?string {
        $state = $component->getState();

        if (strlen($state) <= $component->getCharacterLimit()) {
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

## Limiting text to a specific number of lines

You may want to limit text to a specific number of lines instead of limiting it to a fixed length. Clamping text to a number of lines is useful in responsive interfaces where you want to ensure a consistent experience across all screen sizes. This can be achieved using the `lineClamp()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->lineClamp(2)
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

#### Expanding the limited list

You can allow the limited items to be expanded and collapsed, using the `expandableLimitedList()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
    ->expandableLimitedList()
```

Please note that this is only a feature for `listWithLineBreaks()` or `bulleted()`, where each item is on its own line.

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

If you use this method, then the HTML will be sanitized to remove any potentially unsafe content before it is rendered. If you'd like to opt out of this behavior, you can wrap the HTML in an `HtmlString` object by formatting it:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

TextEntry::make('description')
    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state))
```

Or, you can return a `view()` object from the `formatStateUsing()` method, which will also not be sanitized:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Contracts\View\View;

TextEntry::make('description')
    ->formatStateUsing(fn (string $state): View => view(
        'filament.infolists.components.description-entry-content',
        ['state' => $state],
    ))
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
use Filament\Support\Enums\IconPosition;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
    ->iconPosition(IconPosition::After) // `IconPosition::Before` or `IconPosition::After`
```

<AutoScreenshot name="infolists/entries/text/icon-after" alt="Text entry with icon after" version="3.x" />

The icon color defaults to the text color, but you may customize the icon color separately using `iconColor()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('email')
    ->icon('heroicon-m-envelope')
    ->iconColor('primary')
```

<AutoScreenshot name="infolists/entries/text/icon-color" alt="Text entry with icon in the primary color" version="3.x" />

## Customizing the text size

Text columns have small font size by default, but you may change this to `TextEntrySize::ExtraSmall`, `TextEntrySize::Medium`, or `TextEntrySize::Large`.

For instance, you may make the text larger using `size(TextEntrySize::Large)`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->size(TextEntry\TextEntrySize::Large)
```

<AutoScreenshot name="infolists/entries/text/large" alt="Text entry in a large font size" version="3.x" />

## Customizing the font weight

Text entries have regular font weight by default, but you may change this to any of the following options: `FontWeight::Thin`, `FontWeight::ExtraLight`, `FontWeight::Light`, `FontWeight::Medium`, `FontWeight::SemiBold`, `FontWeight::Bold`, `FontWeight::ExtraBold` or `FontWeight::Black`.

For instance, you may make the font bold using `weight(FontWeight::Bold)`:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

TextEntry::make('title')
    ->weight(FontWeight::Bold)
```

<AutoScreenshot name="infolists/entries/text/bold" alt="Text entry in a bold font" version="3.x" />

## Customizing the font family

You can change the text font family to any of the following options: `FontFamily::Sans`, `FontFamily::Serif` or `FontFamily::Mono`.

For instance, you may make the font monospaced using `fontFamily(FontFamily::Mono)`:

```php
use Filament\Support\Enums\FontFamily;
use Filament\Infolists\Components\TextEntry;

TextEntry::make('apiKey')
    ->label('API key')
    ->fontFamily(FontFamily::Mono)
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
