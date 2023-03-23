---
title: Plugin development
---

Plugins can be used to extend Filament's default behaviour and create reusable modules for use in multiple applications.

To create a new plugin, extend the `Filament\PluginServiceProvider` class provided by Filament:

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
    
    // ...
}
```

The `PluginServiceProvider` extends the service provider from [Laravel Package Tools](https://github.com/spatie/laravel-package-tools), so all `configurePackage()` options available there are available here as well.

Plugins must have a unique name property.

## Registering plugins

### Application plugins

If you're developing a plugin for a specific application, you should register the new service provider in your `config/app.php` file:

```php
return [

    'providers' => [
        // ...
        \App\Providers\ExampleServiceProvider::class,
    ]

];
```

Laravel will load your service provider when bootstrapping and your plugin will be initialised.

### Distributed plugins

Much like a normal Laravel package, you should add your service provider's fully qualified class name to the `extra.laravel.providers` array in your package's `composer.json` file:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Vendor\\Package\\ExampleServiceProvider"
            ]
        }
    }
}
```

This will ensure your service provider is automatically loaded by Laravel when the package is installed.

## Resources

To register a custom resource, add the fully qualified class name to the `$resources` property in your service provider:

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Vendor\Package\Resources\CustomResource;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        CustomResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

Filament will automatically register your `Resource` and ensure that Livewire can discover it.

## Pages

To register a custom page, add the fully qualified class name to the `$pages` property in your service provider:

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Vendor\Package\Pages\CustomPage;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
        CustomPage::class,
    ];
    
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

Filament will automatically register your `Page` and ensure that Livewire can discover it.

## Widgets

To register a custom widget, add the fully qualified class name to the `$widgets` property in your service provider:

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Vendor\Package\Widgets\CustomWidget;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $widgets = [
        CustomWidget::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

Filament will automatically register your `Widget` and ensure that Livewire can discover it.

## Frontend assets

Filament plugins can also register their own frontend assets. These assets will be included on all Filament related pages, allowing you to use your own CSS and JavaScript.

### Stylesheets

To include a custom stylesheet, add it to the `$styles` property in your service provider. You should use a unique name as the key and the URL to the stylesheet as the value.

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $styles = [
        'my-package-styles' => __DIR__ . '/../dist/app.css',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

#### Tailwind CSS

If you are using Tailwind classes, that are not used in Filament core, you need to compile your own Tailwind CSS file and bundle it with your plugin. Follow the Tailwind instructions for setup, but omit `@tailwind base` as this would overwrite the base styles if users customize their Filament theme.

After compilation, your Tailwind stylesheet may contain classes that are already used in Filament core. You should purge those classes with [awcodes/filament-plugin-purge](https://github.com/awcodes/filament-plugin-purge) to keep the stylesheets size low.

### Scripts

To include a custom script, add it to the `$scripts` property in your service provider. You should use a unique name as the key and the URL to the script as the value. These scripts will be added after the core Filament script.

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $scripts = [
        'my-package-scripts' => __DIR__ . '/../dist/app.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

To add scripts before the core Filament script, use the `$beforeCoreScripts` property. This is useful if you want to hook into an Alpine event.

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected array $beforeCoreScripts = [
        'my-package-scripts' => __DIR__ . '/../dist/app.js',
    ];
    
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
}
```

### Providing data to the frontend

Whilst building your plugin, you might find the need to generate some data on the server and access it on the client.

To do this, use the `getScriptData()` method on your service provider and return an array of `string` keys and values that can be passed to converted into JSON:

```php
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
    
    protected function getScriptData(): array
    {
        return [
            'userId' => Auth::id(),
        ];
    }
}
```

You may now access this data in your scripts:

```blade
<script>
    console.log(window.filamentData.userId)
</script>
```

## User menu

To register [user menu items](navigation#customizing-the-user-menu) from your plugin, return them from the `getUserMenuItems()` method in your service provider:

```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Vendor\Package\Pages\CustomPage;

class ExampleServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
    
    protected function getUserMenuItems(): array
    {
        return [
            UserMenuItem::make()
                ->label('Settings')
                ->url(route('filament.pages.settings'))
                ->icon('heroicon-s-cog'),
        ];
    }
}
```

Filament will automatically register your `Page` and ensure that Livewire can discover it.

## Commands, views, translations, migrations and more

Since the `PluginServiceProvider` extends the service provider from [Laravel Package Tools](https://github.com/spatie/laravel-package-tools), you can use the [`configurePackage` method](https://github.com/spatie/laravel-package-tools#usage) to register [commands](https://github.com/spatie/laravel-package-tools#registering-commands), [views](https://github.com/spatie/laravel-package-tools#working-with-views), [translations](https://github.com/spatie/laravel-package-tools#working-with-translations), [migrations](https://github.com/spatie/laravel-package-tools#working-with-migrations) and more.

## ServingFilament Event

If you rely on data defined by Filament on `boot()` or through `Filament::serving()`, you can register listeners for the `Filament\Events\ServingFilament` event:

```php
use Filament\Events\ServingFilament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;

class ExampleServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('your-package-name');
    }
    
    public function packageConfiguring(Package $package): void
    {
        Event::listen(ServingFilament::class, [$this, 'registerStuff']);
    }

    protected function registerStuff(ServingFilament $event): void
    {
        // ...
    }
}

```
