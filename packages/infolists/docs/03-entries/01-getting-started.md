---
title: Getting started
---

## Overview

Entry classes can be found in the `Filament\Infolists\Components` namespace. You can put them inside the `$infolist->entries()` method:

```php
use Filament\Infolists\Infolist;

public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->entries([
            // ...
        ]);
}
```

Entries may be created using the static `make()` method, passing its unique name. You may use "dot notation" to access entries within relationships.

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')

TextEntry::make('author.name')
```

## Available entries

- [Text entry](text)
- [Icon entry](icon)
- [Image entry](image)
- [Color entry](color)

You may also [create your own custom entries](custom) to display data however you wish.

## Setting a label

By default, the label of the entry, which is displayed in the header of the infolist, is generated from the name of the entry. You may customize this using the `label()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->label('Post title')
```

Optionally, you can have the label automatically translated [using Laravel's localization features](https://laravel.com/docs/localization) with the `translateLabel()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->translateLabel() // Equivalent to `label(__('Title'))`
```

## Entry URLs

When an entry is clicked, you may open a URL.

### Opening URLs

To open a URL, you may use the `url()` method, passing a callback or static URL to open. Callbacks accept a `$record` parameter which you may use to customize the URL:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
```

You may also choose to open the URL in a new tab:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
    ->openUrlInNewTab()
```

## Setting a default value

To set a default value for fields with a `null` state, you may use the `default()` method:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->default('Untitled')
```

## Hiding entries

To hide an entry conditionally, you may use the `hidden()` and `visible()` methods, whichever you prefer:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('role')
    ->hidden(! auth()->user()->isAdmin())
// or
TextEntry::make('role')
    ->visible(auth()->user()->isAdmin())
```

## Calculated state

Sometimes you need to calculate the state of an entry, instead of directly reading it from a database entry.

By passing a callback function to the `getStateUsing()` method, you can customize the returned state for that entry:

```php
Infolists\Components\TextEntry::make('amount_including_vat')
    ->getStateUsing(function (Model $record): float {
        return $record->amount * (1 + $record->vat_rate);
    })
```

## Tooltips

> If you want to use tooltips outside of the app framework, make sure you have [`@ryangjchandler/alpine-tooltip` installed](https://github.com/ryangjchandler/alpine-tooltip#installation) in your app, including [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager). You'll also need to install [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager) if you're using a [custom app framework theme](../../app/themes).

You may specify a tooltip to display when you hover over an entry:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('title')
    ->tooltip('Title')
```

This method also accepts a closure that can access the current infolist record:

```php
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

TextEntry::make('title')
    ->tooltip(fn (Model $record): string => "By {$record->author->name}")
```

## Custom attributes

The HTML of entries can be customized, by passing an array of `extraAttributes()`:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('slug')
    ->extraAttributes(['class' => 'bg-gray-200'])
```

These get merged onto the outer `<div>` element of each entry in that entry.

## Global settings

If you wish to change the default behaviour of all entries globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the entries using. For example, if you wish to make all `TextEntry` components [`wrap()`](text#wrapping-content), you can do it like so:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::configureUsing(function (TextEntry $entry): void {
    $entry
        ->wrap();
});
```

Of course, you are still able to overwrite this on each entry individually:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('name')
    ->wrap(false)
```
