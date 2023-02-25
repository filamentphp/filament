---
title: File upload
---

The file upload field is based on [Filepond](https://pqina.nl/filepond).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
```

![](https://user-images.githubusercontent.com/41773797/147613556-62c62153-4d21-4801-8a71-040d528d5757.png)

> Filament also supports [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary). See our [plugin documentation](/docs/spatie-laravel-media-library-plugin) for more information.

## Configuring the storage disk and directory

By default, files will be uploaded publicly to your default storage disk.

To change the disk and directory that files are saved in, and their visibility, use the `disk()`, `directory()` and `visibility` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('s3')
    ->directory('form-attachments')
    ->visibility('private')
```

> Please note, it is the responsibility of the developer to delete these files from the disk if they are removed, as Filament is unaware if they are depended on elsewhere. One way to do this automatically is observing a [model event](https://laravel.com/docs/eloquent#events).

## Uploading multiple files

You may also upload multiple files. This stores URLs in JSON:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')->multiple()
```

If you're saving the file URLs using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $casts = [
        'attachments' => 'array',
    ];

    // ...
}
```

## Customizing file names

By default, a random file name will be generated for newly-uploaded files. To instead preserve the original filenames of the uploaded files, use the `preserveFilenames()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')->preserveFilenames()
```

You may completely customize how file names are generated using the `getUploadedFileNameForStorageUsing()` method, and returning a string from the callback:

```php
use Livewire\TemporaryUploadedFile;

FileUpload::make('attachment')
    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
        return (string) str($file->getClientOriginalName())->prepend('custom-prefix-');
    })
```

### Storing original file names independently

You can keep the randomly generated file names, while still storing the original file name, using the `storeFileNamesIn()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->storeFileNamesIn('attachment_file_names')
```

`attachment_file_names` will now store the original file name/s of your uploaded files.

## Validation

> To customize Livewire's default file upload validation rules, please refer to its [documentation](https://laravel-livewire.com/docs/file-uploads#global-validation).

### File type validation

You may restrict the types of files that may be uploaded using the `acceptedFileTypes()` method, and passing an array of MIME types. You may also use the `image()` method as shorthand to allow all image MIME types.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('document')->acceptedFileTypes(['application/pdf'])
FileUpload::make('image')->image()
```

### File size validation

You may also restrict the size of uploaded files, in kilobytes:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->minSize(512)
    ->maxSize(1024)
```

### Number of files validation

You may customize the number of files that may be uploaded, using the `minFiles()` and `maxFiles()` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->minFiles(2)
    ->maxFiles(5)
```

## Manipulating images

Filepond allows you to crop and resize images before they are uploaded. You can customize this behaviour using the `imageResizeMode()`, `imageCropAspectRatio()`, `imageResizeTargetHeight()` and `imageResizeTargetWidth()` methods. `imageResizeMode()` should be set for the other methods to have an effect - either [`force`, `cover`, or `contain`](https://pqina.nl/filepond/docs/api/plugins/image-resize).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageResizeMode('cover')
    ->imageCropAspectRatio('16:9')
    ->imageResizeTargetWidth('1920')
    ->imageResizeTargetHeight('1080')
```

## Altering the appearance of the file upload area

You may also alter the general appearance of the Filepond component. Available options for these methods are available on the [Filepond website](https://pqina.nl/filepond/docs/api/instance/properties/#styles).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->imagePreviewHeight('250')
    ->loadingIndicatorPosition('left')
    ->panelAspectRatio('2:1')
    ->panelLayout('integrated')
    ->removeUploadedFileButtonPosition('right')
    ->uploadButtonPosition('left')
    ->uploadProgressIndicatorPosition('left')
```

![](https://user-images.githubusercontent.com/41773797/147613590-9ee07ce7-a43e-46a0-bb40-7a21a3692aea.png)

## Reordering files

You can also enable the re-ordering of uploaded files using the `enableReordering()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableReordering()
```

## Opening files in a new tab

You can add a button to open each file in a new tab with the `enableOpen()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableOpen()
```

## Downloading files

If you wish to add a download button to each file instead, you can use the `enableDownload()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableDownload()
```
