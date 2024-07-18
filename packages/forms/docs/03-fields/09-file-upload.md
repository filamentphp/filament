---
title: File upload
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

<LaracastsBanner
    title="File Uploads"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you the basics of adding file upload fields to Filament forms."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/8"
    series="rapid-laravel-development"
/>

The file upload field is based on [Filepond](https://pqina.nl/filepond).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
```

<AutoScreenshot name="forms/fields/file-upload/simple" alt="File upload" version="3.x" />

> Filament also supports [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary). See our [plugin documentation](/plugins/filament-spatie-media-library) for more information.

## Configuring the storage disk and directory

By default, files will be uploaded publicly to your storage disk defined in the [configuration file](../installation#publishing-configuration). You can also set the `FILAMENT_FILESYSTEM_DISK` environment variable to change this.

> To correctly preview images and other files, FilePond requires files to be served from the same domain as the app, or the appropriate CORS headers need to be present. Ensure that the `APP_URL` environment variable is correct, or modify the [filesystem](https://laravel.com/docs/filesystem) driver to set the correct URL. If you're hosting files on a separate domain like S3, ensure that CORS headers are set up.

To change the disk and directory for a specific field, and the visibility of files, use the `disk()`, `directory()` and `visibility()` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('s3')
    ->directory('form-attachments')
    ->visibility('private')
```

> It is the responsibility of the developer to delete these files from the disk if they are removed, as Filament is unaware if they are depended on elsewhere. One way to do this automatically is observing a [model event](https://laravel.com/docs/eloquent#events).

## Uploading multiple files

You may also upload multiple files. This stores URLs in JSON:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
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

## Controlling file names

By default, a random file name will be generated for newly-uploaded files. This is to ensure that there are never any conflicts with existing files.

### Security implications of controlling file names

Before using the `preserveFilenames()` or `getUploadedFileNameForStorageUsing()` methods, please be aware of the security implications. If you allow users to upload files with their own file names, there are ways that they can exploit this to upload malicious files. **This applies even if you use the [`acceptedFileTypes()`](#file-type-validation) method** to restrict the types of files that can be uploaded, since it uses Laravel's `mimetypes` rule which does not validate the extension of the file, only its mime type, which could be manipulated.

This is specifically an issue with the `getClientOriginalName()` method on the `TemporaryUploadedFile` object, which the `preserveFilenames()` method uses. By default, Livewire generates a random file name for each file uploaded, and uses the mime type of the file to determine the file extension.

Using these methods **with the `local` or `public` filesystem disks** will make your app vulnerable to remote code execution if the attacker uploads a PHP file with a deceptive mime type. **Using an S3 disk protects you from this specific attack vector**, as S3 will not execute PHP files in the same way that your server might when serving files from local storage.

If you are using the `local` or `public` disk, you should consider using the [`storeFileNamesIn()` method](#storing-original-file-names-independently) to store the original file names in a separate column in your database, and keep the randomly generated file names in the file system. This way, you can still display the original file names to users, while keeping the file system secure.

On top of this security issue, you should also be aware that allowing users to upload files with their own file names can lead to conflicts with existing files, and can make it difficult to manage your storage. Users could upload files with the same name and overwrite the other's content if you do not scope them to a specific directory, so these features should in all cases only be accessible to trusted users.

### Preserving original file names

> Important: Before using this feature, please ensure that you have read the [security implications](#security-implications-of-controlling-file-names).

To preserve the original filenames of the uploaded files, use the `preserveFilenames()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->preserveFilenames()
```

### Generating custom file names

> Important: Before using this feature, please ensure that you have read the [security implications](#security-implications-of-controlling-file-names).

You may completely customize how file names are generated using the `getUploadedFileNameForStorageUsing()` method, and returning a string from the closure based on the `$file` that was uploaded:

```php
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

FileUpload::make('attachment')
    ->getUploadedFileNameForStorageUsing(
        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
            ->prepend('custom-prefix-'),
    )
```

### Storing original file names independently

You can keep the randomly generated file names, while still storing the original file name, using the `storeFileNamesIn()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->storeFileNamesIn('attachment_file_names')
```

`attachment_file_names` will now store the original file names of your uploaded files, so you can save them to the database when the form is submitted. If you're uploading `multiple()` files, make sure that you add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to this Eloquent model property too.

## Avatar mode

You can enable avatar mode for your file upload field using the `avatar()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('avatar')
    ->avatar()
```

This will only allow images to be uploaded, and when they are, it will display them in a compact circle layout that is perfect for avatars.

This feature pairs well with the [circle cropper](#allowing-users-to-crop-images-as-a-circle).

## Image editor

You can enable an image editor for your file upload field using the `imageEditor()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
```

You can open the editor once you upload an image by clicking the pencil icon. You can also open the editor by clicking the pencil icon on an existing image, which will remove and re-upload it on save.

### Allowing users to crop images to aspect ratios

You can allow users to crop images to a set of specific aspect ratios using the `imageEditorAspectRatios()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios([
        '16:9',
        '4:3',
        '1:1',
    ])
```

You can also allow users to choose no aspect ratio, "free cropping", by passing `null` as an option:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios([
        null,
        '16:9',
        '4:3',
        '1:1',
    ])
```

### Setting the image editor's mode

You can change the mode of the image editor using the `imageEditorMode()` method, which accepts either `1`, `2` or `3`. These options are explained in the [Cropper.js documentation](https://github.com/fengyuanchen/cropperjs#viewmode):

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
    ->imageEditorMode(2)
```

### Customizing the image editor's empty fill color

By default, the image editor will make the empty space around the image transparent. You can customize this using the `imageEditorEmptyFillColor()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
    ->imageEditorEmptyFillColor('#000000')
```

### Setting the image editor's viewport size

You can change the size of the image editor's viewport using the `imageEditorViewportWidth()` and `imageEditorViewportHeight()` methods, which generate an aspect ratio to use across device sizes:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageEditor()
    ->imageEditorViewportWidth('1920')
    ->imageEditorViewportHeight('1080')
```

### Allowing users to crop images as a circle

You can allow users to crop images as a circle using the `circleCropper()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->avatar()
    ->imageEditor()
    ->circleCropper()
```

This is perfectly accompanied by the [`avatar()` method](#avatar-mode), which renders the images in a compact circle layout.

### Cropping and resizing images without the editor

Filepond allows you to crop and resize images before they are uploaded, without the need for a separate editor. You can customize this behavior using the `imageCropAspectRatio()`, `imageResizeTargetHeight()` and `imageResizeTargetWidth()` methods. `imageResizeMode()` should be set for these methods to have an effect - either [`force`, `cover`, or `contain`](https://pqina.nl/filepond/docs/api/plugins/image-resize).

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

### Displaying files in a grid

You can use the [Filepond `grid` layout](https://pqina.nl/filepond/docs/api/style/#grid-layout) by setting the `panelLayout()`:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->panelLayout('grid')
```

## Reordering files

You can also allow users to re-order uploaded files using the `reorderable()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->reorderable()
```

When using this method, FilePond may add newly-uploaded files to the beginning of the list, instead of the end. To fix this, use the `appendFiles()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->reorderable()
    ->appendFiles()
```

## Opening files in a new tab

You can add a button to open each file in a new tab with the `openable()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->openable()
```

## Downloading files

If you wish to add a download button to each file instead, you can use the `downloadable()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->downloadable()
```

## Previewing files

By default, some file types can be previewed in FilePond. If you wish to disable the preview for all files, you can use the `previewable(false)` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->previewable(false)
```

## Moving files instead of copying when the form is submitted

By default, files are initially uploaded to Livewire's temporary storage directory, and then copied to the destination directory when the form is submitted. If you wish to move the files instead, providing that temporary uploads are stored on the same disk as permanent files, you can use the `moveFiles()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->moveFiles()
```

## Preventing files from being stored permanently

If you wish to prevent files from being stored permanently when the form is submitted, you can use the `storeFiles(false)` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->storeFiles(false)
```

When the form is submitted, a temporary file upload object will be returned instead of a permanently stored file path. This is perfect for temporary files like imported CSVs.

Please be aware that images, video and audio files will not show the stored file name in the form's preview, unless you use [`previewable(false)`](#previewing-files). This is due to a limitation with the FilePond preview plugin.

## Orienting images from their EXIF data

By default, FilePond will automatically orient images based on their EXIF data. If you wish to disable this behavior, you can use the `orientImagesFromExif(false)` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->orientImagesFromExif(false)
```

## Hiding the remove file button

It is also possible to hide the remove uploaded file button by using `deletable(false)`:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->deletable(false)
```

## Prevent file information fetching

While the form is loaded, it will automatically detect whether the files exist, what size they are, and what type of files they are. This is all done on the backend. When using remote storage with many files, this can be time-consuming. You can use the `fetchFileInformation(false)` method to disable this feature:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->fetchFileInformation(false)
```

## Customizing the uploading message

You may customize the uploading message that is displayed in the form's submit button using the `uploadingMessage()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->uploadingMessage('Uploading attachment...')
```

## File upload validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to file uploads.

Since Filament is powered by Livewire and uses its file upload system, you will want to refer to the default Livewire file upload validation rules in the `config/livewire.php` file as well. This also controls the 12MB file size maximum.

### File type validation

You may restrict the types of files that may be uploaded using the `acceptedFileTypes()` method, and passing an array of MIME types.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('document')
    ->acceptedFileTypes(['application/pdf'])
```

You may also use the `image()` method as shorthand to allow all image MIME types.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
```

### File size validation

You may also restrict the size of uploaded files in kilobytes:

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
