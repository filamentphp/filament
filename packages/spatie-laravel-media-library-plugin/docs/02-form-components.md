---
title: Form Components
---

To use the media library file upload, you may pass a `model()` to the component:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
    ->model(auth()->user()),
```

When a model instance is attached, files will automatically be populated [and saved](/docs/forms/getting-started#field-relationships) to that model. Alternatively, if you do not have access to a model instance, you may [attach the model instance after the form is saved](/docs/forms/getting-started#saving-field-relationships-manually).

Optionally, you may pass a `collection()`:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
    ->collection('avatars')
    ->model(auth()->user()),
```

The [collection](https://spatie.be/docs/laravel-medialibrary/v9/working-with-media-collections/simple-media-collections) allows you to group files into categories.

The media library file upload supports all the customization options of the [original file upload component](/docs/forms/fields#file-upload).

You may also upload multiple files:

```php
use Filament\Forms\Components\SpatieMediaLibraryMultipleFileUpload;

SpatieMediaLibraryMultipleFileUpload::make('documents')
    ->collection('documents')
    ->model(auth()->user()),
```
