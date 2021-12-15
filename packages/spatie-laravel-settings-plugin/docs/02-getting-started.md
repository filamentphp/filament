---
title: Getting Started
---

## Preparing your page class

Settings pages are Filament pages that extend the `Filament\Pages\SettingsPage` class.

You can create a settings page using the following command:

```bash
php artisan make:filament-settings-page ManageSite --settings=SiteSettings
```

By default this command presumes that you have a `SiteSettings` class in your `app/Settings` directory:

```php
protected static string $settings = SiteSettings::class;
```

To override the default namespace, use the `--namespace | -N` flag as follow:

```bash
php artisan make:filament-settings-page ManageSite --settings=Filament/Settings/SiteSettings -N
```

The `-N` flag will honor your provided namespace and will use it.

```php
use Filament\Settings\SiteSettings; //[tl! focus]
use Filament\Pages\SettingsPage;

class SiteSettings extends SettingsPage
{
    ...
    protected static string $settings = SiteSettings::class;//[tl! focus]
    ...
}
```

This plugin uses the [Spatie's Laravel Setting](https://github.com/spatie/laravel-settings) package under the hood, so for information on how to setup your settings class and other options consult there.

## Building a form

Since the [form builder](/docs/forms) is installed in the admin panel by default, you may use any form [fields](/docs/forms/fields) or [layout components](/docs/forms/layout) you like, including those from Filament plugins:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
 
protected function getFormSchema(): array
{
    return [
        TextInput::make('copyright')
            ->label('Copyright notice')
            ->required(),
        Repeater::make('links')
            ->schema([
                TextInput::make('label')->required(),
                TextInput::make('url')
                    ->url()
                    ->required(),
            ]),
    ];
}
```
The name of each form field must correspond with the name of the property on your [Settings](https://github.com/spatie/laravel-settings) class.
