---
title: Text entry
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Text entries display simple text:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
```

<AutoScreenshot name="infolists/entries/text/simple" alt="Text entry" version="4.x" />

## Customizing the color

You may set a [color](../styling/colors) for the text:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->color('primary')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `color()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/color" alt="Text entry in the primary color" version="4.x" />

## Adding an icon

Text entries may also have an [icon](../styling/icons):

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;

TextEntry::make('email')
    ->icon(Heroicon::Envelope)
```

<UtilityInjection set="infolistEntries" version="4.x">The `icon()` method also accepts a function to dynamically calculate the icon. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/icon" alt="Text entry with icon" version="4.x" />

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

TextEntry::make('email')
    ->icon(Heroicon::Envelope)
    ->iconPosition(IconPosition::After) // `IconPosition::Before` or `IconPosition::After`
```

<UtilityInjection set="infolistEntries" version="4.x">The `iconPosition()` method also accepts a function to dynamically calculate the icon position. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/icon-after" alt="Text entry with icon after" version="4.x" />

The icon color defaults to the text color, but you may customize the icon [color](../styling/colors) separately using `iconColor()`:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;

TextEntry::make('email')
    ->icon(Heroicon::Envelope)
    ->iconColor('primary')
```

<UtilityInjection set="infolistEntries" version="4.x">The `iconColor()` method also accepts a function to dynamically calculate the icon color. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/icon-color" alt="Text entry with icon in the primary color" version="4.x" />

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

<AutoScreenshot name="infolists/entries/text/badge" alt="Text entry as badge" version="4.x" />

You may add other things to the badge, like an [icon](#adding-an-icon).

Optionally, you may pass a boolean value to control if the text should be in a badge or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->badge(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `badge()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Formatting

When using a text entry, you may want the actual outputted text in the UI to differ from the raw [state](overview#entry-content-state) of the entry, which is often automatically retrieved from an Eloquent model. Formatting the state allows you to preserve the integrity of the raw data while also allowing it to be presented in a more user-friendly way.

To format the state of a text entry without changing the state itself, you can use the `formatStateUsing()` method. This method accepts a function that takes the state as an argument and returns the formatted state:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

In this case, the `status` column in the database might contain values like `draft`, `reviewing`, `published`, or `rejected`, but the formatted state will be the translated version of these values.

<UtilityInjection set="infolistEntries" version="4.x">The function passed to `formatStateUsing()` can inject various utilities as parameters.</UtilityInjection>

### Date formatting

Instead of passing a function to `formatStateUsing()`, you may use the `date()`, `dateTime()`, and `time()` methods to format the entry's state using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->date()

TextEntry::make('created_at')
    ->dateTime()

TextEntry::make('created_at')
    ->time()
```

You may customize the date format by passing a custom format string to the `date()`, `dateTime()`, or `time()` method. You may use any [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->date('M j, Y')
    
TextEntry::make('created_at')
    ->dateTime('M j, Y H:i:s')
    
TextEntry::make('created_at')
    ->time('H:i:s')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `date()`, `dateTime()`, and `time()` methods also accept a function to dynamically calculate the format. You can inject various utilities into the function as parameters.</UtilityInjection>

#### Date formatting using Carbon macro formats

You may use also the `isoDate()`, `isoDateTime()`, and `isoTime()` methods to format the entry's state using [Carbon's macro-formats](https://carbon.nesbot.com/docs/#available-macro-formats):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->isoDate()

TextEntry::make('created_at')
    ->isoDateTime()

TextEntry::make('created_at')
    ->isoTime()
```

You may customize the date format by passing a custom macro format string to the `isoDate()`, `isoDateTime()`, or `isoTime()` method. You may use any [Carbon's macro-formats](https://carbon.nesbot.com/docs/#available-macro-formats):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->isoDate('L')

TextEntry::make('created_at')
    ->isoDateTime('LLL')

TextEntry::make('created_at')
    ->isoTime('LT')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `isoDate()`, `isoDateTime()`, and `isoTime()` methods also accept a function to dynamically calculate the format. You can inject various utilities into the function as parameters.</UtilityInjection>

#### Relative date formatting

You may use the `since()` method to format the entry's state using [Carbon's `diffForHumans()`](https://carbon.nesbot.com/docs/#api-humandiff):

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->since()
```

#### Displaying a formatting date in a tooltip

Additionally, you can use the `dateTooltip()`, `dateTimeTooltip()`, `timeTooltip()`, `isoDateTooltip()`, `isoDateTimeTooltip()`, `isoTime()`, `isoTimeTooltip()`, or `sinceTooltip()` method to display a formatted date in a [tooltip](overview#adding-a-tooltip-to-an-entry), often to provide extra information:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->since()
    ->dateTooltip() // Accepts a custom PHP date formatting string

TextEntry::make('created_at')
    ->since()
    ->dateTimeTooltip() // Accepts a custom PHP date formatting string

TextEntry::make('created_at')
    ->since()
    ->timeTooltip() // Accepts a custom PHP date formatting string

TextEntry::make('created_at')
    ->since()
    ->isoDateTooltip() // Accepts a custom Carbon macro format string

TextEntry::make('created_at')
    ->since()
    ->isoDateTimeTooltip() // Accepts a custom Carbon macro format string

TextEntry::make('created_at')
    ->since()
    ->isoTimeTooltip() // Accepts a custom Carbon macro format string

TextEntry::make('created_at')
    ->dateTime()
    ->sinceTooltip()
```

#### Setting the timezone for date formatting

Each of the date formatting methods listed above also accepts a `timezone` argument, which allows you to convert the time set in the state to a different timezone:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->dateTime(timezone: 'America/New_York')
```

You can also pass a timezone to the `timezone()` method of the entry to apply a timezone to all date-time formatting methods at once:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')
    ->timezone('America/New_York')
    ->dateTime()
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `timezone()` method also accepts a function to dynamically calculate the timezone. You can inject various utilities into the function as parameters.</UtilityInjection>

If you do not pass a `timezone()` to the entry, it will use Filament's default timezone. You can set Filament's default timezone using the `FilamentTimezone::set()` method in the `boot()` method of a service provider such as `AppServiceProvider`:

```php
use Filament\Support\Facades\FilamentTimezone;

public function boot(): void
{
    FilamentTimezone::set('America/New_York');
}
```

This is useful if you want to set a default timezone for all text entries in your application. It is also used in other places where timezones are used in Filament.

<Aside variant="warning">
    Filament's default timezone will only apply when the entry stores a time. If the entry stores a date only (`date()` instead of `dateTime()`), the timezone will not be applied. This is to prevent timezone shifts when storing dates without times.
</Aside>

### Number formatting

Instead of passing a function to `formatStateUsing()`, you can use the `numeric()` method to format an entry as a number:

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

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `decimalPlaces` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, your app's locale will be used to format the number suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('stock')
    ->numeric(locale: 'nl')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `locale` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Money formatting

Instead of passing a function to `formatStateUsing()`, you can use the `money()` method to easily format amounts of money, in any currency:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `money()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

There is also a `divideBy` argument for `money()` that allows you to divide the original value by a number before formatting it. This could be useful if your database stores the price in cents, for example:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR', divideBy: 100)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `divideBy` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, your app's locale will be used to format the money suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR', locale: 'nl')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `locale` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

If you would like to customize the number of decimal places used to format the number with, you can use the `decimalPlaces` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('price')
    ->money('EUR', decimalPlaces: 3)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `decimalPlaces` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Rendering Markdown

If your entry value is Markdown, you may render it using `markdown()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->markdown()
```

Optionally, you may pass a boolean value to control if the text should be rendered as Markdown or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->markdown(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `markdown()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Rendering HTML

If your entry value is HTML, you may render it using `html()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->html()
```

Optionally, you may pass a boolean value to control if the text should be rendered as HTML or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->html(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `html()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

#### Rendering raw HTML without sanitization

If you use this method, then the HTML will be sanitized to remove any potentially unsafe content before it is rendered. If you'd like to opt out of this behavior, you can wrap the HTML in an `HtmlString` object by formatting it:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

TextEntry::make('description')
    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state))
```

<Aside variant="danger">
    Be cautious when rendering raw HTML, as it may contain malicious content, which can lead to security vulnerabilities in your app such as cross-site scripting (XSS) attacks. Always ensure that the HTML you are rendering is safe before using this method.
</Aside>

Alternatively, you can return a `view()` object from the `formatStateUsing()` method, which will also not be sanitized:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Contracts\View\View;

TextEntry::make('description')
    ->formatStateUsing(fn (string $state): View => view(
        'filament.infolists.components.description-entry-content',
        ['state' => $state],
    ))
```

## Listing multiple values

Multiple values can be rendered in a text entry if its [state](overview#entry-content-state) is an array. This can happen if you are using an `array` cast on an Eloquent attribute, an Eloquent relationship with multiple results, or if you have passed an array to the [`state()` method](overview#setting-the-state-of-an-entry). If there are multiple values inside your text entry, they will be comma-separated. You may use the `listWithLineBreaks()` method to display them on new lines instead:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
```

<AutoScreenshot name="infolists/entries/text/list" alt="Text entry with multiple values" version="4.x" />

Optionally, you may pass a boolean value to control if the text should have line breaks between each item or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `listWithLineBreaks()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Adding bullet points to the list

You may add a bullet point to each list item using the `bulleted()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->bulleted()
```

<AutoScreenshot name="infolists/entries/text/bullet-list" alt="Text entry with multiple values and bullet points" version="4.x" />

Optionally, you may pass a boolean value to control if the text should have bullet points or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->bulleted(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `bulleted()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Limiting the number of values in the list

You can limit the number of values in the list using the `limitList()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `limitList()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

#### Expanding the limited list

You can allow the limited items to be expanded and collapsed, using the `expandableLimitedList()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
    ->expandableLimitedList()
```

<Aside variant="info">
    This is only a feature for `listWithLineBreaks()` or `bulleted()`, where each item is on its own line.
</Aside>

Optionally, you may pass a boolean value to control if the text should be expandable or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('authors.name')
    ->listWithLineBreaks()
    ->limitList(3)
    ->expandableLimitedList(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `expandableLimitedList()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Splitting a single value into multiple list items

If you want to "explode" a text string from your model into multiple list items, you can do so with the `separator()` method. This is useful for displaying comma-separated tags [as badges](#displaying-as-a-badge), for example:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('tags')
    ->badge()
    ->separator(',')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `separator()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Aggregating relationships

Filament provides several methods for aggregating a relationship field, including `avg()`, `max()`, `min()` and `sum()`. For instance, if you wish to show the average of a field on all related records, you may use the `avg()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('users_avg_age')->avg('users', 'age')
```

In this example, `users` is the name of the relationship, while `age` is the field that is being averaged. The name of the entry must be `users_avg_age`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#other-aggregate-functions) for storing the result.

If you'd like to scope the relationship before aggregating, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;

TextEntry::make('users_avg_age')->avg([
    'users' => fn (Builder $query) => $query->where('is_active', true),
], 'age')
```

## Customizing the text size

Text entries have small font size by default, but you may change this to `TextSize::ExtraSmall`, `TextSize::Medium`, or `TextSize::Large`.

For instance, you may make the text larger using `size(TextSize::Large)`:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;

TextEntry::make('title')
    ->size(TextSize::Large)
```

<AutoScreenshot name="infolists/entries/text/large" alt="Text entry in a large font size" version="4.x" />

## Customizing the font weight

Text entries have regular font weight by default, but you may change this to any of the following options: `FontWeight::Thin`, `FontWeight::ExtraLight`, `FontWeight::Light`, `FontWeight::Medium`, `FontWeight::SemiBold`, `FontWeight::Bold`, `FontWeight::ExtraBold` or `FontWeight::Black`.

For instance, you may make the font bold using `weight(FontWeight::Bold)`:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

TextEntry::make('title')
    ->weight(FontWeight::Bold)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `weight()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/bold" alt="Text entry in a bold font" version="4.x" />

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

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `fontFamily()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/text/mono" alt="Text entry in a monospaced font" version="4.x" />

## Handling long text

### Limiting text length

You may `limit()` the length of the entry's value:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->limit(50)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `limit()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, when text is truncated, an ellipsis (`...`) is appended to the end of the text. You may customize this by passing a custom string to the `end` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->limit(50, end: ' (more)')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `end` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

You may also reuse the value that is being passed to `limit()` in a function, by getting it using the `getCharacterLimit()` method:

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

### Limiting word count

You may limit the number of `words()` displayed in the entry:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->words(10)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `words()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, when text is truncated, an ellipsis (`...`) is appended to the end of the text. You may customize this by passing a custom string to the `end` argument:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->words(10, end: ' (more)')
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `end` argument also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Limiting text to a specific number of lines

You may want to limit text to a specific number of lines instead of limiting it to a fixed length. Clamping text to a number of lines is useful in responsive interfaces where you want to ensure a consistent experience across all screen sizes. This can be achieved using the `lineClamp()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->lineClamp(2)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `lineClamp()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Preventing text wrapping

By default, text will wrap to the next line if it exceeds the width of the container. You can prevent this behavior using the `wrap(false)` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->wrap(false)
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `wrap()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Allowing the text to be copied to the clipboard

You may make the text copyable, such that clicking on the entry copies the text to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('apiKey')
    ->label('API key')
    ->copyable()
    ->copyMessage('Copied!')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="infolists/entries/text/copyable" alt="Text entry with a button to copy it" version="4.x" />

Optionally, you may pass a boolean value to control if the text should be copyable or not:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('apiKey')
    ->label('API key')
    ->copyable(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `copyable()`, `copyMessage()`, and `copyMessageDuration()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="warning">
    This feature only works when SSL is enabled for the app.
</Aside>
