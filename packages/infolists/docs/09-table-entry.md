---
title: Table entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The table entry allows you to render a simple table of data from an array (or collection) of rows.

```php
use Filament\Infolists\Components\TableEntry;

TableEntry::make('users')
```

For example, the state of this entry might be represented as:

```php
[
    ['Dan Harrin', 'dan@filamentphp.com', 'Admin'],
    ['Jane Doe', 'jane@example.com', 'Editor'],
]
```

You may also provide optional column labels to display in the table header:

```php
use Filament\Infolists\Components\TableEntry;

TableEntry::make('users')
    ->columnLabels([
        'Name', 'Email', 'Role',
    ])
```

<AutoScreenshot name="infolists/entries/table/simple" alt="Table entry" version="4.x" />

## Customizing the column labels

You may customize the labels for the table columns using the `columnLabels()` method:

```php
use Filament\Infolists\Components\TableEntry;

TableEntry::make('users')
    ->columnLabels([
        'Name', 'Email', 'Role',
    ])
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing a static value, the `columnLabels()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
