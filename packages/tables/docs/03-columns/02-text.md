---
title: Text column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Text columns display simple text from your database:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
```

<AutoScreenshot name="tables/columns/text/simple" alt="Text column" version="3.x" />

## Displaying as a "badge"

By default, the text is quite plain and has no background color. You can make it appear as a "badge" instead using the `badge()` method. A great use case for this is with statuses, where may want to display a badge with a [color](#customizing-the-color) that matches the status:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        'rejected' => 'danger',
    })
```

<AutoScreenshot name="tables/columns/text/badge" alt="Text column as badge" version="3.x" />

You may add other things to the badge, like an [icon](#adding-an-icon).

## Displaying a description

Descriptions may be used to easily render additional text above or below the column contents.

You can display a description below the contents of a text column using the `description()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->description(fn (Post $record): string => $record->description)
```

<AutoScreenshot name="tables/columns/text/description" alt="Text column with description" version="3.x" />

By default, the description is displayed below the main text, but you can move it above using the second parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->description(fn (Post $record): string => $record->description, position: 'above')
```

<AutoScreenshot name="tables/columns/text/description-above" alt="Text column with description above the content" version="3.x" />

## Date formatting

You may use the `date()` and `dateTime()` methods to format the column's state using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')
    ->dateTime()
```

You may use the `since()` method to format the column's state using [Carbon's `diffForHumans()`](https://carbon.nesbot.com/docs/#api-humandiff):

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')
    ->since()
```

## Number formatting

The `numeric()` method allows you to format an entry as a number:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('stock')
    ->numeric()
```

If you would like to customize the number of decimal places used to format the number with, you can use the `decimalPlaces` argument:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('stock')
    ->numeric(decimalPlaces: 0)
```

By default, your app's locale will be used to format the number suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('stock')
    ->numeric(locale: 'nl')
```

Alternatively, you can set the default locale used across your app using the `Number::useLocale()` method in the `boot()` method of a service provider:

```php
use Illuminate\Support\Number;

Number::useLocale('nl');
```

## Currency formatting

The `money()` method allows you to easily format monetary values, in any currency:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->money('EUR')
```

There is also a `divideBy` argument for `money()` that allows you to divide the original value by a number before formatting it. This could be useful if your database stores the price in cents, for example:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->money('EUR', divideBy: 100)
```

By default, your app's locale will be used to format the money suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->money('EUR', locale: 'nl')
```

Alternatively, you can set the default locale used across your app using the `Number::useLocale()` method in the `boot()` method of a service provider:

```php
use Illuminate\Support\Number;

Number::useLocale('nl');
```

## Limiting text length

You may `limit()` the length of the cell's value:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->limit(50)
```

You may also reuse the value that is being passed to `limit()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->limit(50)
    ->tooltip(function (TextColumn $column): ?string {
        $state = $column->getState();

        if (strlen($state) <= $column->getCharacterLimit()) {
            return null;
        }

        // Only render the tooltip if the column content exceeds the length limit.
        return $state;
    })
```

## Limiting word count

You may limit the number of `words()` displayed in the cell:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->words(10)
```

## Limiting text to a specific number of lines

You may want to limit text to a specific number of lines instead of limiting it to a fixed length. Clamping text to a number of lines is useful in responsive interfaces where you want to ensure a consistent experience across all screen sizes. This can be achieved using the `lineClamp()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->lineClamp(2)
```

## Adding a prefix or suffix

You may add a prefix or suffix to the cell's value:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('domain')
    ->prefix('https://')
    ->suffix('.com')
```

## Wrapping content

If you'd like your column's content to wrap if it's too long, you may use the `wrap()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->wrap()
```

## Listing multiple values

By default, if there are multiple values inside your text column, they will be comma-separated. You may use the `listWithLineBreaks()` method to display them on new lines instead:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('authors.name')
    ->listWithLineBreaks()
```

### Adding bullet points to the list

You may add a bullet point to each list item using the `bulleted()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('authors.name')
    ->listWithLineBreaks()
    ->bulleted()
```

### Limiting the number of values in the list

You can limit the number of values in the list using the `limitList()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
```

#### Expanding the limited list

You can allow the limited items to be expanded and collapsed, using the `expandableLimitedList()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
    ->expandableLimitedList()
```

Please note that this is only a feature for `listWithLineBreaks()` or `bulleted()`, where each item is on its own line.

### Using a list separator

If you want to "explode" a text string from your model into multiple list items, you can do so with the `separator()` method. This is useful for displaying comma-separated tags [as badges](#displaying-as-a-badge), for example:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('tags')
    ->badge()
    ->separator(',')
```

## Rendering HTML

If your column value is HTML, you may render it using `html()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->html()
```

If you use this method, then the HTML will be sanitized to remove any potentially unsafe content before it is rendered. If you'd like to opt out of this behavior, you can wrap the HTML in an `HtmlString` object by formatting it:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

TextColumn::make('description')
    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state))
```

Or, you can return a `view()` object from the `formatStateUsing()` method, which will also not be sanitized:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

TextColumn::make('description')
    ->formatStateUsing(fn (string $state): View => view(
        'filament.tables.columns.description-entry-content',
        ['state' => $state],
    ))
```

### Rendering Markdown as HTML

If your column contains Markdown, you may render it using `markdown()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('description')
    ->markdown()
```

## Custom formatting

You may instead pass a custom formatting callback to `formatStateUsing()`, which accepts the `$state` of the cell, and optionally its `$record`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

## Customizing the color

You may set a color for the text, either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->color('primary')
```

<AutoScreenshot name="tables/columns/text/color" alt="Text column in the primary color" version="3.x" />

## Adding an icon

Text columns may also have an icon:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->icon('heroicon-m-envelope')
```

<AutoScreenshot name="tables/columns/text/icon" alt="Text column with icon" version="3.x" />

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->icon('heroicon-m-envelope')
    ->iconPosition(IconPosition::After) // `IconPosition::Before` or `IconPosition::After`
```

<AutoScreenshot name="tables/columns/text/icon-after" alt="Text column with icon after" version="3.x" />

The icon color defaults to the text color, but you may customize the icon color separately using `iconColor()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->icon('heroicon-m-envelope')
    ->iconColor('primary')
```

<AutoScreenshot name="tables/columns/text/icon-color" alt="Text column with icon in the primary color" version="3.x" />

## Customizing the text size

Text columns have small font size by default, but you may change this to `TextColumnSize::ExtraSmall`, `TextColumnSize::Medium`, or `TextColumnSize::Large`.

For instance, you may make the text larger using `size(TextColumnSize::Large)`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->size(TextColumn\TextColumnSize::Large)
```

<AutoScreenshot name="tables/columns/text/large" alt="Text column in a large font size" version="3.x" />

## Customizing the font weight

Text columns have regular font weight by default, but you may change this to any of the following options: `FontWeight::Thin`, `FontWeight::ExtraLight`, `FontWeight::Light`, `FontWeight::Medium`, `FontWeight::SemiBold`, `FontWeight::Bold`, `FontWeight::ExtraBold` or `FontWeight::Black`.

For instance, you may make the font bold using `weight(FontWeight::Bold)`:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->weight(FontWeight::Bold)
```

<AutoScreenshot name="tables/columns/text/bold" alt="Text column in a bold font" version="3.x" />

## Customizing the font family

You can change the text font family to any of the following options: `FontFamily::Sans`, `FontFamily::Serif` or `FontFamily::Mono`.

For instance, you may make the font mono using `fontFamily(FontFamily::Mono)`:

```php
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->fontFamily(FontFamily::Mono)
```

<AutoScreenshot name="tables/columns/text/mono" alt="Text column in a monospaced font" version="3.x" />

## Allowing the text to be copied to the clipboard

You may make the text copyable, such that clicking on the cell copies the text to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->copyable()
    ->copyMessage('Email address copied')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="tables/columns/text/copyable" alt="Text column with a button to copy it" version="3.x" />

### Customizing the text that is copied to the clipboard

You can customize the text that gets copied to the clipboard using the `copyableState()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('url')
    ->copyable()
    ->copyableState(fn (string $state): string => "URL: {$state}")
```

In this function, you can access the whole table row with `$record`:

```php
use App\Models\Post;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('url')
    ->copyable()
    ->copyableState(fn (Post $record): string => "URL: {$record->url}")
```

## Displaying the row index

You may want a column to contain the number of the current row in the table:

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;

TextColumn::make('index')->state(
    static function (HasTable $livewire, stdClass $rowLoop): string {
        return (string) (
            $rowLoop->iteration +
            ($livewire->getTableRecordsPerPage() * (
                $livewire->getTablePage() - 1
            ))
        );
    }
),
```

As `$rowLoop` is [Laravel Blade's `$loop` object](https://laravel.com/docs/blade#the-loop-variable), you can reference all other `$loop` properties.

As a shortcut, you may use the `rowIndex()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('index')
    ->rowIndex()
```

To start counting from 0 instead of 1, use `isFromZero: true`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('index')
    ->rowIndex(isFromZero: true)
```
