---
title: Image column
---

Images can be easily displayed within your table:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('header_image')
```

The column in the database must contain the path to the image, relative to the root directory of its storage disk.

## Managing the image disk

By default, the `public` disk will be used to retrieve images. You may pass a custom disk name to the `disk()` method:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('header_image')->disk('s3')
```

## Private images

Filament can generate temporary URLs to render private images, you may set the `visibility()` to `private`:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('header_image')->visibility('private')
```

## Rounding the image

You may make the image fully `rounded()`, which is useful for rendering avatars:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('author.avatar')->rounded()
```

## Customizing the size

You may customize the image size by passing a `width()` and `height()`, or both with `size()`:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('header_image')->width(200)

ImageColumn::make('header_image')->height(50)

ImageColumn::make('author.avatar')->size(40)
```

## Custom attributes

You may customize the extra HTML attributes of the image using `extraImgAttributes()`:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('logo')
    ->extraImgAttributes(['title' => 'Company logo']),
```
