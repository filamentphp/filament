---
title: Key-value entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The key-value entry allows you to render key-value pairs of data, from a one-dimensional JSON object / PHP array.

```php
use Filament\Infolists\Components\KeyValueEntry;

KeyValueEntry::make('meta')
```

<AutoScreenshot name="infolists/entries/key-value/simple" alt="Key-value entry" version="3.x" />

If you're saving the data in Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'meta' => 'array',
    ];

    // ...
}
```

## Customizing the key column's label

You may customize the label for the key column using the `keyLabel()` method:

```php
use Filament\Infolists\Components\KeyValueEntry;

KeyValueEntry::make('meta')
    ->keyLabel('Property name')
```

## Customizing the value column's label

You may customize the label for the value column using the `valueLabel()` method:

```php
use Filament\Infolists\Components\KeyValueEntry;

KeyValueEntry::make('meta')
    ->valueLabel('Property value')
```
