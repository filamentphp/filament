# Filament Spatie Settings Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-settings-plugin:"^3.0-stable" -W
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

Since the [Form Builder](https://filamentphp.com/docs/forms) is installed in the Panel Builder by default, you may use any form [fields](https://filamentphp.com/docs/forms/fields) or [layout components](https://filamentphp.com/docs/forms/layout) you like:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
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

## Publishing translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-settings-plugin-translations
```

## Upgrading from v2.x to v3.x

### High impact changes

#### The `make:filament-settings-page` command

In v2.x, the command generated settings classes with the `getFormSchema()` method which is no longer automatically
called in v3.x. 

If you have generated settings pages from v2.x, make sure your form is returned with the correct schema by overriding
the `form()` method of `Filament\Pages\SettingsPage` and moving the contents of `getFormSchema()` inside the `schema()`
method:

```php
// v2.x

protected function getFormSchema(): array
{
    return [
        TextInput::make('name')->required()
    ];
}
```

```php
// v3.x

public function form(Form $form): Form
{
    return $form->schema([
            TextInput::make('name')->required(),
        ]);
}
```
