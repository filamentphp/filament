---
title: Textarea
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The textarea allows you to interact with a multi-line string:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
```

<AutoScreenshot name="forms/fields/textarea/simple" alt="Textarea" version="3.x" />

## Resizing the textarea

You may change the size of the textarea by defining the `rows()` and `cols()` methods:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(10)
    ->cols(20)
```

### Autosizing the textarea

You may allow the textarea to automatically resize to fit its content by setting the `autosize()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->autosize()
```

## Textarea validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to textareas.

### Length validation

You may limit the length of the textarea by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->minLength(2)
    ->maxLength(1024)
```

You can also specify the exact length of the textarea by setting the `length()`. This method adds both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('question')
    ->length(100)
```
