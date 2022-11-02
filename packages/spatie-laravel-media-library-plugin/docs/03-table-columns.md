---
title: Table columns
---

To use the media library image column:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar'),
```

The media library image column supports all the customization options of the [original image column](/docs/tables/columns/image).

## Passing a collection

Optionally, you may pass a `collection()`:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')->collection('avatars'),
```

The [collection](https://spatie.be/docs/laravel-medialibrary/working-with-media-collections/simple-media-collections) you to group files into categories.

## Using conversions

You may also specify a `conversion()` to load the file from showing it in the form, if present:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')->conversion('thumb'),
```
