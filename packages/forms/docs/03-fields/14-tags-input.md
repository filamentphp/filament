---
title: Tags input
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The tags input component allows you to interact with a list of tags.

By default, tags are stored in JSON:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
```

<AutoScreenshot name="forms/fields/tags-input/simple" alt="Tags input" version="3.x" />

If you're saving the JSON tags using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    // ...
}
```

> Filament also supports [`spatie/laravel-tags`](https://github.com/spatie/laravel-tags). See our [plugin documentation](/plugins/filament-spatie-tags) for more information.

## Comma-separated tags

You may allow the tags to be stored in a separated string, instead of JSON. To set this up, pass the separating character to the `separator()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->separator(',')
```

## Autocompleting tag suggestions

Tags inputs may have autocomplete suggestions. To enable this, pass an array of suggestions to the `suggestions()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->suggestions([
        'tailwindcss',
        'alpinejs',
        'laravel',
        'livewire',
    ])
```

## Defining split keys

Split keys allow you to map specific buttons on your user's keyboard to create a new tag. By default, when the user presses "Enter", a new tag is created in the input. You may also define other keys to create new tags, such as "Tab" or " ". To do this, pass an array of keys to the `splitKeys()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->splitKeys(['Tab', ' '])
```

You can [read more about possible options for keys](https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/key).

## Adding a prefix and suffix to individual tags

You can add prefix and suffix to tags without modifying the real state of the field. This can be useful if you need to show presentational formatting to users without saving it. This is done with the `tagPrefix()` or `tagSuffix()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('percentages')
    ->tagSuffix('%')
```

## Reordering tags

You can allow the user to reorder tags within the field using the `reorderable()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->reorderable()
```

## Changing the color of tags

You can change the color of the tags by passing a color to the `color()` method. It may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->color('danger')
```

## Tags validation

You may add validation rules for each tag by passing an array of rules to the `nestedRecursiveRules()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->nestedRecursiveRules([
        'min:3',
        'max:255',
    ])
```
