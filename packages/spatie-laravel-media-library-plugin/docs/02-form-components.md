---
title: Form Components
---

## File upload

To use the media library file upload, you may pass a `model()` to the component:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
    ->model(auth()->user()),
```

When a model instance is attached, files will automatically be populated [and saved](/docs/forms/building-forms#field-relationships) to that model. Alternatively, if you do not have access to a model instance, you may [attach the model instance after the form is saved](/docs/forms/building-forms#saving-field-relationships-manually).

Optionally, you may pass a `collection()`:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
    ->collection('avatars')
    ->model(auth()->user()),
```

The [collection](https://spatie.be/docs/laravel-medialibrary/v9/working-with-media-collections/simple-media-collections) you to group files into categories.

The media library file upload supports all the customization options of the [original file upload component](/docs/forms/fields#file-upload).

You may also upload multiple files:

```php
use Filament\Forms\Components\SpatieMediaLibraryMultipleFileUpload;

SpatieMediaLibraryMultipleFileUpload::make('documents')
    ->collection('documents')
    ->model(auth()->user()),
```
