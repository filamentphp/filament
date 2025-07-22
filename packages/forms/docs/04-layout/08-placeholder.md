---
title: Placeholder
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Placeholders can be used to render text-only "fields" within your forms. Each placeholder has `content()`, which cannot be changed by the user.

```php
use App\Models\Post;
use Filament\Forms\Components\Placeholder;

Placeholder::make('created')
    ->content(fn (Post $record): string => $record->created_at->toFormattedDateString())
```

<AutoScreenshot name="forms/layout/placeholder/simple" alt="Placeholder" version="3.x" />

> **Important:** All form fields require a unique name. That also applies to Placeholders!

## Rendering HTML inside the placeholder

You may even render custom HTML within placeholder content:

```php
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

Placeholder::make('documentation')
    ->content(new HtmlString('<a href="https://filamentphp.com/docs">filamentphp.com</a>'))
```

## Dynamically generating placeholder content

By passing a closure to the `content()` method, you may dynamically generate placeholder content. You have access to any closure parameter explained in the [advanced closure customization](../advanced#closure-customization) documentation:

```php
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;

Placeholder::make('total')
    ->content(function (Get $get): string {
        return '€' . number_format($get('cost') * $get('quantity'), 2);
    })
```

## Allowing the content to be copied to the clipboard

You may make the content copyable, such that clicking on content the value to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Forms\Components\Placeholder;

Placeholder::make('placeholder')
    ->content('My content')
    ->copyable()
    ->copyMessage('Content copied')
    ->copyMessageDuration(1500)
```

### Customizing the text that is copied to the clipboard

You can customize the text that gets copied to the clipboard using the `copyableState()` method:

```php
use Filament\Forms\Components\Placeholder;

Placeholder::make('placeholder')
    ->content('My content')
    ->copyable()
    ->copyableState(fn (string $state): string => "Placeholder: {$state}")
```

In this function, you can access the whole table row with `$record`:

```php
use App\Models\Post;
use Filament\Forms\Components\Placeholder;

Placeholder::make('placeholder')
    ->content('My content')
    ->copyable()
    ->copyableState(fn (Post $record): string => "Record: {$record->name}")
```