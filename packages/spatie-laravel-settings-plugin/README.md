# Filament Spatie Settings Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-settings-plugin:"^4.0" -W
```

## Preparing your page class

Settings pages are Filament pages that extend the `Filament\Pages\SettingsPage` class.

This package uses the [`spatie/laravel-settings` package](https://github.com/spatie/laravel-settings) to store and retrieve settings via the database.

Before you start, create a settings class in your `app/Settings` directory, and a database migration for it. You can find out more about how to do this in the [Spatie documentation](https://github.com/spatie/laravel-settings#usage).

Once you've created your settings class, you can create a settings page in Filament for it using the following command:

```bash
php artisan make:filament-settings-page ManageFooter FooterSettings
```

In this example, you have a `FooterSettings` class in your `app/Settings` directory.

In your new settings page class, generated in the `app/Filament/Pages` directory, you will see the static `$settings` property assigned to the settings class:

```php
protected static string $settings = FooterSettings::class;
```

## Building a form

You must define a form schema to interact with your settings class inside the `form()` method.

Since the [Form Builder](https://filamentphp.com/docs/forms) is installed in the Panel Builder by default, you may use any form [fields](https://filamentphp.com/docs/forms) or [layout components](https://filamentphp.com/docs/schemas/layout) you like:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

public function form(Schema $schema): Schema
{
    return $schema
        ->components([
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
        ]);
}
```

The name of each form field must correspond with the name of the property on your settings class.

The form will automatically be filled with settings from the database, and saved without any extra work.

## Authorization

By default, no users are restricted from accessing settings pages. To prevent a page from being accessed altogether, define a `canAccess()` method in the page class, and return `true` or `false` based on the user's permissions:

```php
public static function canAccess(): bool
{
    return auth()->user()->isAdmin();
}
```

If the user has access to the page, you can also prevent them from editing the settings. This is useful if a user needs to be able to check the current state of the settings, but not change them. In this case, the settings form would be "disabled" and read-only. Define the `canEdit()` method in the page class, and return `true` or `false` based on the user's permissions:

```php
public static function canEdit(): bool
{
    return auth()->user()->isAdmin();
}
```

## Publishing translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-settings-plugin-translations
```
