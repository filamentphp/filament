---
title: Building Settings Pages
---

## Preparing your page class

Settings pages are Filament pages that extend the `Filament\Pages\SettingsPage` class.

You can create a settings page using the following command:

```bash
php artisan make:filament-settings-page ManageFooter FooterSettings
```

This command presumes that you have a `FooterSettings` class in your `app/Settings` directory:

```php
protected static string $settings = FooterSettings::class;
```

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
                TextInput::make('url')->url()->required(),
            ]),
    ];
}
```

The name of each form field must correspond with the name of the property on your settings class.
