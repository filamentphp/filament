# Filament Spatie Translatable Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-translatable-plugin:"^3.0"
```

## Preparing your resource class

First, you must apply the `Filament\Resources\Concerns\Translatable` trait to your resource class. This adds a `getTranslatableLocales()` method, which you may override to return an array of locales that the resource can be translated into:

```php
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;

class BlogPostResource extends Resource
{
    use Translatable;
    
    // ...
    
    public static function getTranslatableLocales(): array
    {
        return ['en', 'es'];
    }
}
```

## Making resource pages translatable

After [preparing your resource class](#preparing-your-resource-class), you must make each of your resource's pages translatable too. You can find your resource's pages in the `Pages` directory of each resource folder:

```php
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogPosts extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

```php
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

```php
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

And if you have a `ViewRecord` page for your resource:

```php
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

## Publishing translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-translatable-plugin-translations
```