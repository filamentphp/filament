# Filament Spatie Media Library Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-media-library-plugin:"^4.0" -W
```

If you haven't already done so, you need to publish the migration to create the media table:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

You must also [prepare your Eloquent model](https://spatie.be/docs/laravel-medialibrary/basic-usage/preparing-your-model) for attaching media.

> For more information, check out [Spatie's documentation](https://spatie.be/docs/laravel-medialibrary).

## Form component

You may use the field in the same way as the [original file upload](https://filamentphp.com/docs/forms/file-upload) field:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
```

The media library file upload supports all the customization options of the [original file upload component](https://filamentphp.com/docs/forms/file-upload).

### Passing a collection

Optionally, you may pass a [`collection()`](https://spatie.be/docs/laravel-medialibrary/working-with-media-collections/simple-media-collections) that allows you to group files into categories:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
    ->collection('avatars')
```

### Configuring the storage disk and directory

By default, files will be uploaded publicly to your storage disk defined in the [Filament configuration file](https://filamentphp.com/docs/forms/installation#publishing-configuration). You can also set the `FILESYSTEM_DISK` environment variable to change this. This is to ensure consistency between all Filament packages. Spatie's disk configuration will not be used, unless you [define a disk for a registered collection](https://spatie.be/docs/laravel-medialibrary/working-with-media-collections/defining-media-collections#content-using-a-specific-disk).

Alternatively, you can manually set the disk with the `disk()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('s3')
```

The base file upload component also has configuration options for setting the `directory()` and `visibility()` of uploaded files. These are not used by the media library file upload component. Spatie's package has its own system for determining the directory of a newly-uploaded file, and it does not support uploading private files out of the box. One way to store files privately is to configure this in your S3 bucket settings, in which case you should also use `visibility('private')` to ensure that Filament generates temporary URLs for your files.

### Reordering files

In addition to the behavior of the normal file upload, Spatie's Media Library also allows users to reorder files.

To enable this behavior, use the `reorderable()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->reorderable()
```

You may now drag and drop files into order.

### Adding custom properties

You may pass in [custom properties](https://spatie.be/docs/laravel-medialibrary/advanced-usage/using-custom-properties) when uploading files using the `customProperties()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->customProperties(['zip_filename_prefix' => 'folder/subfolder/'])
```

You may use a function to dynamically set the properties based on the uploaded file:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Image;

SpatieMediaLibraryFileUpload::make('image')
    ->image()
    ->customProperties(function (TemporaryUploadedFile $file): array {
        $image = Image::load($file->getRealPath());

        return [
            'height' => $image->getHeight(),
            'width' => $image->getWidth(),
        ];
    })
```

### Adding custom headers

You may pass in custom headers when uploading files using the `customHeaders()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->customHeaders(['CacheControl' => 'max-age=86400'])
```

### Generating responsive images

You may [generate responsive images](https://spatie.be/docs/laravel-medialibrary/responsive-images/getting-started-with-responsive-images) when the files are uploaded using the `responsiveImages()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->responsiveImages()
```

### Using conversions

You may also specify a `conversion()` to load the file from showing it in the form, if present:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->conversion('thumb')
```

#### Storing conversions on a separate disk

You can store your conversions and responsive images on a disk other than the one where you save the original file. Pass the name of the disk where you want conversion to be saved to the `conversionsDisk()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->conversionsDisk('s3')
```

### Storing media-specific manipulations

You may pass in [manipulations](https://spatie.be/docs/laravel-medialibrary/advanced-usage/storing-media-specific-manipulations#breadcrumb) that are run when files are uploaded using the `manipulations()` method:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->manipulations([
        'thumb' => ['orientation' => '90'],
    ])
```

### Filtering media

It's possible to target a file upload component to only handle a certain subset of media in a collection. To do that, you can filter the media collection using the `filterMediaUsing()` method. This method accepts a function that receives the `$media` collection and manipulates it. You can use any [collection method](https://laravel.com/docs/collections#available-methods) to filter it.

For example, you could scope the field to only handle media that has certain custom properties:

```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Collection;

SpatieMediaLibraryFileUpload::make('images')
    ->customProperties(fn (Get $get): array => [
        'gallery_id' => $get('gallery_id'),
    ])
    ->filterMediaUsing(
        fn (Collection $media, Get $get): Collection => $media->where(
            'custom_properties.gallery_id',
            $get('gallery_id')
        ),
    )
```

### Using media library for rich editor file attachments

You can use media library to store file attachments in the [rich editor](https://filamentphp.com/docs/forms/rich-editor). To do this, you must [register a rich content attribute](https://filamentphp.com/docs/forms/rich-editor#registering-rich-content-attributes) on your model, similar to how a media library collection is registered. You should call `fileAttachmentProvider()` on the attribute registration, passing in a `SpatieMediaLibraryFileAttachmentProvider::make()` object:

```php
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasRichContent
{
    use InteractsWithRichContent;

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make());
    }
}
```

> Using `SpatieMediaLibraryFileAttachmentProvider` requires that the rich content attribute (`content` in this example) must be defined as nullable in database.

A media collection with the same name as the attribute (`content` in this example) will be used for the file attachments. The collection must not contain any other media apart from file attachments for that attribute, since Filament will clear any unused media from the collection when the model is saved. To customize the name of the collection, you can pass it to the `collection()` method of the provider:

```php
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasRichContent
{
    use InteractsWithRichContent;

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentProvider(
                SpatieMediaLibraryFileAttachmentProvider::make()
                    ->collection('content-file-attachments'),
            );
    }
}
```

You may want to preserve the original filenames of the uploaded files, using the `preserveFilenames()` method:

```php
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;

SpatieMediaLibraryFileAttachmentProvider::make()
    ->preserveFilenames()
```

You can customize the [media name](https://spatie.be/docs/laravel-medialibrary/api/adding-files#content-usingname) using the `mediaName()` method:

```php
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

SpatieMediaLibraryFileAttachmentProvider::make()
    ->mediaName(fn (TemporaryUploadedFile $file): string => Str::random() . '_' . $file->getClientOriginalName())
```

You may pass in [custom properties](https://spatie.be/docs/laravel-medialibrary/advanced-usage/using-custom-properties) when uploading files using the `customProperties()` method:

```php
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;

SpatieMediaLibraryFileAttachmentProvider::make()
    ->customProperties(['archived' => false])
```

## Table column

To use the media library image column:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
```

The media library image column supports all the customization options of the [original image column](https://filamentphp.com/docs/tables/columns/image).

### Passing a collection

Optionally, you may pass a `collection()`:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
    ->collection('avatars')
```

The [collection](https://spatie.be/docs/laravel-medialibrary/working-with-media-collections/simple-media-collections) allows you to group files into categories.

By default, only media without a collection (using the `default` collection) will be shown. If you want to show media from all collections, you can use the `allCollections()` method:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
    ->allCollections()
```

### Using conversions

You may also specify a `conversion()` to load the file from showing it in the table, if present:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
    ->conversion('thumb')
```

### Filtering media

It's possible to target the column to only display a subset of media in a collection. To do that, you can filter the media collection using the `filterMediaUsing()` method. This method accepts a function that receives the `$media` collection and manipulates it. You can use any [collection method](https://laravel.com/docs/collections#available-methods) to filter it.

For example, you could scope the column to only display media that has certain custom properties:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Collection;

SpatieMediaLibraryImageColumn::make('images')
    ->filterMediaUsing(
        fn (Collection $media): Collection => $media->where(
            'custom_properties.gallery_id',
            12345,
        ),
    )
```

## Infolist entry

To use the media library image entry:

```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
```

The media library image entry supports all the customization options of the [original image entry](https://filamentphp.com/docs/infolists/entries/image).

### Passing a collection

Optionally, you may pass a `collection()`:

```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
    ->collection('avatars')
```

The [collection](https://spatie.be/docs/laravel-medialibrary/working-with-media-collections/simple-media-collections) allows you to group files into categories.

By default, only media without a collection (using the `default` collection) will be shown. If you want to show media from all collections, you can use the `allCollections()` method:

```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
    ->allCollections()
```

### Using conversions

You may also specify a `conversion()` to load the file from showing it in the infolist, if present:

```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
    ->conversion('thumb')
```

### Filtering media

It's possible to target the entry to only display a subset of media in a collection. To do that, you can filter the media collection using the `filterMediaUsing()` method. This method accepts a function that receives the `$media` collection and manipulates it. You can use any [collection method](https://laravel.com/docs/collections#available-methods) to filter it.

For example, you could scope the entry to only display media that has certain custom properties:

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageEntry;
use Illuminate\Support\Collection;

SpatieMediaLibraryImageEntry::make('images')
    ->filterMediaUsing(
        fn (Collection $media): Collection => $media->where(
            'custom_properties.gallery_id',
            12345,
        ),
    )
```
