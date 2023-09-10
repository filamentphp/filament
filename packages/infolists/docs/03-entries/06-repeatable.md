---
title: Repeatable entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The repeatable entry allows you to repeat a set of entries and layout components for items in an array or relationship.

```php
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

RepeatableEntry::make('comments')
    ->schema([
        TextEntry::make('author.name'),
        TextEntry::make('title'),
        TextEntry::make('content')
            ->columnSpan(2),
    ])
    ->columns(2)
```

As you can see, the repeatable entry has an embedded `schema()` which gets repeated for each item.

<AutoScreenshot name="infolists/entries/repeatable/simple" alt="Repeatable entry" version="3.x" />

## Grid layout

You may organize repeatable items into columns by using the `grid()` method:

```php
use Filament\Infolists\Components\RepeatableEntry;

RepeatableEntry::make('comments')
    ->schema([
        // ...
    ])
    ->grid(2)
```

This method accepts the same options as the `columns()` method of the [grid](../layout/grid). This allows you to responsively customize the number of grid columns at various breakpoints.

<AutoScreenshot name="infolists/entries/repeatable/grid" alt="Repeatable entry in grid layout" version="3.x" />

## Removing the styled container

By default, each item in a repeatable entry is wrapped in a container styled as a card. You may remove the styled container using `contained()`:

```php
use Filament\Infolists\Components\RepeatableEntry;

RepeatableEntry::make('comments')
    ->schema([
        // ...
    ])
    ->contained(false)
```
