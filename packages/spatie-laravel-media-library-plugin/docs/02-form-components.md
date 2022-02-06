---
title: Form Components
---

You may use the field in the same way as the [original file upload](/docs/forms/fields#file-upload) field:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar'),
```

> The field will automatically load and save its uploads to your model. To set this functionality up, **you must also follow the instructions set out in the [field relationships](/docs/forms/getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

Optionally, you may pass a [`collection()`](https://spatie.be/docs/laravel-medialibrary/v9/working-with-media-collections/simple-media-collections) allows you to group files into categories:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')->collection('avatars'),
```

The media library file upload supports all the customization options of the [original file upload component](/docs/forms/fields#file-upload).

### Reordering files

In addition to the behaviour of the normal file upload, Spatie's Media Library also allows users to reorder files.

To enable this behaviour, use the `enableReordering()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->enableReordering(),
```

You may now drag and drop files into order.
