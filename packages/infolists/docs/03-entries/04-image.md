---
title: Image entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Images can be easily displayed within your infolist:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('header_image')
```

The entry must contain the path to the image, relative to the root directory of its storage disk, or an absolute URL to it.

<AutoScreenshot name="infolists/entries/image/simple" alt="Image entry" version="3.x" />

## Managing the image disk

By default, the `public` disk will be used to retrieve images. You may pass a custom disk name to the `disk()` method:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('header_image')
    ->disk('s3')
```

## Private images

Filament can generate temporary URLs to render private images, you may set the `visibility()` to `private`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('header_image')
    ->visibility('private')
```

## Square image

You may display the image using a 1:1 aspect ratio:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('author.avatar')
    ->square()
```

<AutoScreenshot name="infolists/entries/image/square" alt="Square image entry" version="3.x" />

## Circular image

You may make the image fully rounded, which is useful for rendering avatars:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('author.avatar')
    ->circular()
```

<AutoScreenshot name="infolists/entries/image/circular" alt="Circular image entry" version="3.x" />

## Customizing the size

You may customize the image size by passing a `width()` and `height()`, or both with `size()`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('header_image')
    ->width(200)

ImageEntry::make('header_image')
    ->height(50)

ImageEntry::make('author.avatar')
    ->size(40)
```

## Adding a default image URL

You can display a placeholder image if one doesn't exist yet, by passing a URL to the `defaultImageUrl()` method:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('avatar')
    ->defaultImageUrl(url('/images/placeholder.png'))
```

## Custom attributes

You may customize the extra HTML attributes of the image using `extraImgAttributes()`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('logo')
    ->extraImgAttributes([
        'alt' => 'Logo',
        'loading' => 'lazy',
    ]),
```
