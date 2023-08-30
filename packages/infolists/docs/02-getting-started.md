---
title: Getting started
---

## Overview

Filament's infolist package allows you to [render a read-only list of data about a particular entity](adding-an-infolist-to-a-livewire-component). It's also used within other Filament packages, such as the [Panel Builder](../panels) for displaying [app resources](../panels/resources/getting-started) and [relation managers](../panels/resources/relation-managers), as well as for [action modals](../actions). Learning the features of the Infolist Builder will be incredibly time-saving when both building your own custom Livewire applications and using Filament's other packages.

This guide will walk you through the basics of building infolists with Filament's infolist package. If you're planning to add a new infolist to your own Livewire component, you should [do that first](adding-an-infolist-to-a-livewire-component) and then come back. If you're adding an infolist to an [app resource](../panels/resources/getting-started), or another Filament package, you're ready to go!

## Defining entries

The first step to building an infolist is to define the entries that will be displayed in the list. You can do this by calling the `schema()` method on an `Infolist` object. This method accepts an array of entry objects.

```php
use Filament\Infolists\Components\TextEntry;

$infolist
    ->schema([
        TextEntry::make('title'),
        TextEntry::make('slug'),
        TextEntry::make('content'),
    ]);
```

Each entry is a piece of information that should be displayed in the infolist. The `TextEntry` is used for displaying text, but there are [other entry types available](entries/getting-started#available-entries).

Infolists within the Panel Builder and other packages usually have 2 columns by default. For custom infolists, you can use the `columns()` method to achieve the same effect:

```php
$infolist
    ->schema([
        // ...
    ])
    ->columns(2);
```

Now, the `content` entry will only consume half of the available width. We can use the `columnSpan()` method to make it span the full width:

```php
use Filament\Infolists\Components\TextEntry;

[
    TextEntry::make('title'),
    TextEntry::make('slug')
    TextEntry::make('content')
        ->columnSpan(2), // or `columnSpan('full')`,
]
```

You can learn more about columns and spans in the [layout documentation](layout/grid). You can even make them responsive!

## Using layout components

The Infolist Builder allows you to use [layout components](layout/getting-started#available-layout-components) inside the schema array to control how entries are displayed. `Section` is a layout component, and it allows you to add a heading and description to a set of entries. It can also allow entries inside it to collapse, which saves space in long infolists.

```php
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

[
    TextEntry::make('title'),
    TextEntry::make('slug'),
    TextEntry::make('content')
        ->columnSpan(2)
        ->markdown(),
    Section::make('Media')
        ->description('Images used in the page layout.')
        ->schema([
            // ...
        ]),
]
```

In this example, you can see how the `Section` component has its own `schema()` method. You can use this to nest other entries and layout components inside:

```php
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

Section::make('Media')
    ->description('Images used in the page layout.')
    ->schema([
        ImageEntry::make('hero_image'),
        TextEntry::make('alt_text'),
    ])
```

This section now contains an [`ImageEntry`](entries/image) and a [`TextEntry`](entries/text). You can learn more about those entries and their functionalities on the respective docs pages.

## Next steps with the infolists package

Now you've finished reading this guide, where to next? Here are some suggestions:

- [Explore the available entries to display data in your infolist.](entries/getting-started#available-entries)
- [Discover how to build complex, responsive layouts without touching CSS.](layout/getting-started)
