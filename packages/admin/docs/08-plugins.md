---
title: Plugin Development
---

Plugins can be used to extend Filament's default behaviour and create reusable modules for use in multiple applications.

To create a new plugin, extend the `Filament\PluginServiceProvider` class provided by Filament:

```php
use Filament\PluginServiceProvider;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    // ...
}
```

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

To register a custom resource, add the fully qualified class name to the `getResources()` method in your service provider.

```php
use Filament\PluginServiceProvider;
use Vendor\Package\Resources\CustomResource;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getResources(): array
    {
        return [
            CustomResource::class,
        ];
    }
}
```

Filament will automatically register your `Resource` and ensure that Livewire can discover it.

## Pages

To register a custom page, add the fully qualified class name to the `getPages()` method in your service provider.

```php
use Filament\PluginServiceProvider;
use Vendor\Package\Pages\CustomPage;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getPages(): array
    {
        return [
            CustomPage::class,
        ];
    }
}
```

Filament will automatically register your `Page` and ensure that Livewire can discover it.

## Widgets

To register a custom widget, add the fully qualified class name to the `getWidgets()` method in your service provider.

```php
use Filament\PluginServiceProvider;
use Vendor\Package\Widgers\CustomWidget;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getWidgets(): array
    {
        return [
            CustomWidget::class,
        ];
    }
}
```

Filament will automatically register your `Widget` and ensure that Livewire can discover it.

## Frontend assets

Filament plugins can also register their own frontend assets. These assets will be included on all Filament related pages, allowing you to use your own CSS and JavaScript.

### Stylesheets

To include a custom stylesheet, add it to the `getStyles()` method in your service provider. You should use a unique name as the key and the URL to the stylesheet as the value.

```php
use Filament\PluginServiceProvider;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getStyles(): array
    {
        return [
            'my-package-styles' => __DIR__ . '/../dist/app.css',
        ];
    }
}
```

### Scripts

To include a custom script, add it to the `getScripts()` method in your service provider. You should use a unique name as the key and the URL to the script as the value.

```php
use Filament\PluginServiceProvider;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getScripts(): array
    {
        return [
            'my-package-scripts' => __DIR__ . '/../dist/app.js',
        ];
    };
}
```

### Providing data to the frontend

Whilst building your plugin, you might find the need to generate some data on the server and access it on the client.

To do this, use the `getScriptData()` method on your service provider and return an array of `string` keys and values that can be passed to converted into JSON:

```php
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Auth;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
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

## Commands

To register commands for your plugin, return them from the `getCommands()` method in your service provider:

```php
use Filament\PluginServiceProvider;
use Vendor\Package\Commands;

class ExampleServiceProvider extends PluginServiceProvider
{
    public static string $name = 'example';
    
    protected function getCommands(): array
    {
        return [
            Commands\CustomCommand::class,
        ];
    }
}
```
