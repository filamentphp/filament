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

## Square image

You may display the image using a 1:1 aspect ratio:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('author.avatar')
    ->height(40)
    ->square()
```

<AutoScreenshot name="infolists/entries/image/square" alt="Square image entry" version="3.x" />

## Circular image

You may make the image fully rounded, which is useful for rendering avatars:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('author.avatar')
    ->height(40)
    ->circular()
```

<AutoScreenshot name="infolists/entries/image/circular" alt="Circular image entry" version="3.x" />

## Adding a default image URL

You can display a placeholder image if one doesn't exist yet, by passing a URL to the `defaultImageUrl()` method:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('avatar')
    ->defaultImageUrl(url('/images/placeholder.png'))
```

## Stacking images

You may display multiple images as a stack of overlapping images by using `stacked()`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
```

<AutoScreenshot name="infolists/entries/image/stacked" alt="Stacked image entry" version="3.x" />

### Customizing the stacked ring width

The default ring width is `3`, but you may customize it to be from `0` to `8`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->ring(5)
```

### Customizing the stacked overlap

The default overlap is `4`, but you may customize it to be from `0` to `8`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->overlap(2)
```

## Setting a limit

You may limit the maximum number of images you want to display by passing `limit()`:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->limit(3)
```

<AutoScreenshot name="infolists/entries/image/limited" alt="Limited image entry" version="3.x" />

### Showing the remaining images count

When you set a limit you may also display the count of remaining images by passing `limitedRemainingText()`.

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->limit(3)
    ->limitedRemainingText()
```

<AutoScreenshot name="infolists/entries/image/limited-remaining-text" alt="Limited image entry with remaining text" version="3.x" />

#### Showing the limited remaining text separately

By default, `limitedRemainingText()` will display the count of remaining images as a number stacked on the other images. If you prefer to show the count as a number after the images, you may use the `isSeparate: true` parameter:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->limit(3)
    ->limitedRemainingText(isSeparate: true)
```

<AutoScreenshot name="infolists/entries/image/limited-remaining-text-separately" alt="Limited image entry with remaining text separately" version="3.x" />

#### Customizing the limited remaining text size

By default, the size of the remaining text is `sm`. You can customize this to be `xs`, `md` or `lg` using the `size` parameter:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('colleagues.avatar')
    ->height(40)
    ->circular()
    ->stacked()
    ->limit(3)
    ->limitedRemainingText(size: 'lg')
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

## Prevent file existence checks

When the infolist is loaded, it will automatically detect whether the images exist. This is all done on the backend. When using remote storage with many images, this can be time-consuming. You can use the `checkFileExistence(false)` method to disable this feature:

```php
use Filament\Infolists\Components\ImageEntry;

ImageEntry::make('attachment')
    ->checkFileExistence(false)
```
