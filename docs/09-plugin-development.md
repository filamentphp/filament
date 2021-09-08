---
title: Plugin Development
---

Plugins can be used to extend Filament's default behaviour and create reusable modules for use in multiple applications.

To create a new plugin, extend the `Filament\PluginServiceProvider` class provided by Filament:

```php
use Filament\PluginServiceProvider;

class ExampleServiceProvider extends PluginServiceProvider
{
    // ...
}
```

## Registering Plugins

### Application Plugins

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

### Distributed Plugins

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

To register a custom resource, add the fully qualified class name to the `protected $resources` array in your service provider.

```php
use Vendor\Package\Resources\CustomResource;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected $resources = [
        CustomResource::class,
    ];
}
```

Filament will automatically register your `Resource` and ensure that Livewire can discover it.

## Pages

To register a custom page, add the fully qualified class name to the `protected $pages` array in your service provider.

```php
use Vendor\Package\Pages\CustomPage;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected $pages = [
        CustomPage::class,
    ];
}
```

Filament will automatically register your `Page` and ensure that Livewire can discover it.

## Widgets

To register a custom widget, add the fully qualified class name to the `protected $widgets` array in your service provider.

```php
use Vendor\Package\Widgers\CustomWidget;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected $widgets = [
        CustomWidget::class,
    ];
}
```

Filament will automatically register your `Widget` and ensure that Livewire can discover it.

## Roles

To register a custom role, add the fully qualified class name to the `protected $roles` array in your service provider.

```php
use Vendor\Package\Roles\CustomRole;

class ExampleServiceProvider extends PluginServiceProvider
{
    protected $roles = [
        CustomRole::class,
    ];
}
```

Filament will automatically register your `Role` and ensure it's available for use throughout your application.

## Frontend Assets

Filament plugins can also register their own frontend assets. These assets will be included on all Filament related pages, allowing you to use your own CSS and JavaScript.

### Stylesheets

To include a custom stylesheet, add it to the `protected $styles` property in your service provider. You should use a unique name as the key and the URL to the stylesheet as the value.

```php
class ExampleServiceProvider extends PluginServiceProvider
{
    protected $styles = [
        'my-package-styles' => '/vendor/my-package/css/style.css',
    ];
}
```

If you need to dynamically generate the key or value, you can overwrite the `protected styles()` method and return an `array` of key/value pairs, just like the `$styles` property:

```php
class ExampleServiceProvider extends PluginServiceProvider
{
    protected function styles()
    {
        return [
            'my-package-styles' => asset('/vendor/my-package/css/style.css'),
        ];
    }
}
```

### Scripts

To include a custom script, add it to the `protected $scripts` property in your service provider. You should use a unique name as the key and the URL to the script as the value.

```php
class ExampleServiceProvider extends PluginServiceProvider
{
    protected $scripts = [
        'my-package-scripts' => '/vendor/my-package/js/main.js'
    ];
}
```

If you need to dynamically generate the key or value, you can overwrite the `protected scripts()` method and return an `array` of key/value pairs, just like the `$scripts` property:

```php
class ExampleServiceProvider extends PluginServiceProvider
{
    protected function scripts()
    {
        return [
            'my-package-scripts' => asset('/vendor/my-package/js/main.js'),
        ];
    }
}
```

### Providing Data to the Frontend

Whilst building your plugin, you might find the need to generate some data on the server and access it on the client.

To do this, add a new `protected function scriptData()` to your service provider and return an array of `string` keys and values that can be passed to converted into JSON.

```php
class ExampleServiceProvider extends PluginServiceProvider
{
    protected function scriptData()
    {
        return [
            'user' => Auth::user(),
        ];
    }
}
```

> Filament uses the `@json` Blade directive to convert your script data into a valid JavaScript object. You can find out more about this directive in the [official Laravel documentation](https://laravel.com/docs/blade#rendering-json).
