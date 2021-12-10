---
title: Getting Started
---

## Usage

Settings pages are Filament pages that extend the `Filament\Pages\SettingsPage` class.

You can create a settings page using the following command:

```bash
php artisan make:filament-settings-page ManageSite SiteSettings
```

This command will create a settings class `SiteSettings`, migration `create_site_settings` and page `ManageSite`

## Preparing your settings class

Define your settings' properties.

```php
use Filament\Settings;

class SiteSettings extends Settings
{
    public string $name;
    public string $slogan;
    public ?string $logo;

    public static function group(): string
    {
        return 'site';
    }
}
```
This plugin uses the [Spatie's Laravel Setting](https://github.com/spatie/laravel-settings) package under the hood, so for more options refer to the main package.
## Preparing your settings' migration

Each property in a settings class needs a default value that should be set in its migration.

```php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSiteSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.name', 'Filament');
        $this->migrator->add('site.slogan', 'The elegant TALLkit for Laravel artisans.');
        $this->migrator->add('site.logo', '');
    }
}
```

## Preparing your page class

The name of each form field must correspond with the name of the property on your settings class:

```php
...
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ManageSite extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = SiteSettings::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Site Name')
                ->required(),
            TextInput::make('slogan')
                ->label('Site Name')
                ->required(),
            FileUpload::make('logo')
                ->required(),
                ->avatar()
        ];
    }
}
```

Since the [form builder](/docs/forms) is installed in the admin panel by default, you may use any form [fields](/docs/forms/fields) or [layout components](/docs/forms/layout) you like, including those from Filament plugins.
