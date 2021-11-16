---
title: Table Columns
---

To use the media library image column:

```php
use Filament\Tables\Components\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar'),
```

Optionally, you may pass a `collection()`:

```php
use Filament\Tables\Components\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')->collection('avatars'),
```

The [collection](https://spatie.be/docs/laravel-medialibrary/v9/working-with-media-collections/simple-media-collections) you to group files into categories.

The media library image column supports all the customization options of the [original image column](/docs/tables/columns#image-column).
