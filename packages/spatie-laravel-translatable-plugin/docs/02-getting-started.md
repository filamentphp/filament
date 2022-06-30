---
title: Getting Started
---

This guide assumes that you've already set up your model to be translatable as per [Spatie's documentation](https://github.com/spatie/laravel-translatable#making-a-model-translatable).

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

You may [publish the package's configuration file](installation#publishing-the-configuration) to set the `default_locales` for all resources at once.

## Making resource pages translatable

After [preparing your resource class](#preparing-your-resource-class), you must make each of your resource's pages translatable too. You can find your resource's pages in the `Pages` directory of each resource folder:

```php
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogPosts extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected function getActions(): array
    {
        LocaleSwitcher::make(),
        // ...
    }
    
    // ...
}
```

```php
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected function getActions(): array
    {
        Actions\LocaleSwitcher::make(),
    }
    
    // ...
}
```

```php
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected function getActions(): array
    {
        Actions\LocaleSwitcher::make(),
        // ...
    }
    
    // ...
}
```

And if you have a `ViewRecord` page for your resource:

```php
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    
    protected function getActions(): array
    {
        Actions\LocaleSwitcher::make(),
        // ...
    }
    
    // ...
}
```
