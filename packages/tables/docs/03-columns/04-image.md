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

## Square image

You may display the image using a 1:1 aspect ratio:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('author.avatar')->square()
```

## Circular image

You may make the image fully rounded, which is useful for rendering avatars:

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('author.avatar')->circular()
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

## Multiple images

You may display multiple images from an array:

```php
ImageColumn::make('images')
    ->circular()
```

Be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'images' => 'array',
    ];

    // ...
}
```

## Stacking images

You may display multiple images as a stack of overlapping images by using `stacked()`:

```php
ImageColumn::make('images')
    ->circular()
    ->stacked()
```

### Setting a limit

You may set a limit to the number of images you want to display by passing `limit()`:

```php
ImageColumn::make('images')
    ->circular()
    ->stacked()
    ->limit(3)
```

### Showing the remaining images count

When you set a limit you may also display the count of remaining images by passing `showRemaining()`. 

```php
ImageColumn::make('images')
    ->circular()
    ->stacked()
    ->limit(3)
    ->showRemaining()
```

By default, `showRemaining()` will display the count of remaining images as a number stacked on the other images. If you prefer to show the count as a number after the images you may use `showRemainingAfterStack()`. You may also set the text size by using `remainingTextSize('xs')`;

### Customizing the ring width

The default ring width is `ring-3` but you may customize the ring width to be either `0`, `1`, `2`, or `4` which correspond to tailwinds `ring-widths`: `ring-0`, `ring-1`, `ring-2`, and `ring-4` respectively.

```php
ImageColumn::make('users.avatar')
    ->circular()
    ->stacked()
    ->ring(3)
```

### Customizing the overlap

The default overlap is `-space-x-1` but you may customize the overlap to be either `0`, `1`, `2`, `3`, or `4` which correspond to tailwinds `space-x` options: `space-x-0`, `-space-x-1`, `-space-x-2`, `-space-x-3`, and `-space-x-4` respectively.

```php
ImageColumn::make('users.avatar')
    ->circular()
    ->stacked()
    ->overlap(3)
```