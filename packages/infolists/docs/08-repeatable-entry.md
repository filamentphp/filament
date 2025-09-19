---
title: Repeatable entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

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

For example, the state of this entry might be represented as:

```php
[
    [
        'author' => ['name' => 'Jane Doe'],
        'title' => 'Wow!',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
    ],
    [
        'author' => ['name' => 'John Doe'],
        'title' => 'This isn\'t working. Help!',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
    ],
]
```

<AutoScreenshot name="infolists/entries/repeatable/simple" alt="Repeatable entry" version="4.x" />

Alternatively, `comments` and `author` could be Eloquent relationships, `title` and `content` could be attributes on the comment model, and `name` could be an attribute on the author model. Filament will automatically handle the relationship loading and display the data in the same way.

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

This method accepts the same options as the `columns()` method of the [grid](../schemas/layouts#grid-system). This allows you to responsively customize the number of grid columns at various breakpoints.

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `grid()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="infolists/entries/repeatable/grid" alt="Repeatable entry in grid layout" version="4.x" />

## Table layout

You may organize repeatable items into a table by using the `table()` method:

```php
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;

RepeatableEntry::make('users')
    ->schema([
        TextEntry::make('name'),
        TextEntry::make('email'),
        TextEntry::make('role'),
    ])
    ->table([
        TableColumn::make('Name'),
        TableColumn::make('Email'),
        TableColumn::make('Role'),
    ])
```

This will display the repeatable data in a structured table format with defined column headers.

<AutoScreenshot name="infolists/entries/repeatable/table" alt="Repeatable entry in table layout" version="4.x" />

### Table column configuration

You can customize table columns with various options:

```php
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Support\Enums\Alignment;

RepeatableEntry::make('products')
    ->schema([
        TextEntry::make('name'),
        TextEntry::make('price'),
        TextEntry::make('status'),
    ])
    ->table([
        TableColumn::make('Product Name')
            ->width('300px'),
        TableColumn::make('Price')
            ->alignment(Alignment::Right)
            ->width('120px'),
        TableColumn::make('Status')
            ->hiddenHeaderLabel(),
    ])
```

#### Column alignment

You can set the alignment of table columns using the `alignment()` method:

```php
TableColumn::make('Price')
    ->alignment(Alignment::Right)
```

#### Column width

You can set the width of table columns using the `width()` method:

```php
TableColumn::make('Name')
    ->width('200px')
```

#### Hidden header labels

You can hide the header label of a column while keeping it accessible to screen readers:

```php
TableColumn::make('Actions')
    ->hiddenHeaderLabel()
```

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

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `contained()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
