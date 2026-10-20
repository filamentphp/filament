---
title: Build a panel plugin
---

## Introduction

In this walkthrough, you'll build a plugin that adds a clock widget to a panel.

You can find the original plugin at [awcodes/clock-widget](https://github.com/awcodes/clock-widget). The examples below use Filament 4 APIs.

## Preface

Please read the docs on [panel plugin development](../plugins/panel-plugins) and the [getting started guide](getting-started) before continuing.

## Step 1: Create the plugin

First, we'll create the plugin using the steps outlined in the [getting started guide](getting-started#creating-a-plugin).

## Step 2: Clean up

Next, we'll clean up the plugin to remove the boilerplate code we don't need. This will seem like a lot, but since this is a simple plugin, we can remove a lot of the boilerplate code.

Remove the following directories and files:
1. `config`
1. `database`
1. `src/Commands`
1. `src/Facades`
1. `stubs`

Since our plugin doesn't have any settings or additional methods needed for functionality, we can also remove the `ClockWidgetPlugin.php` file.

1. `ClockWidgetPlugin.php`

Since Filament recommends that users style their plugins with a custom filament theme, we'll remove the files needed for using CSS in the plugin. This is optional, and you can still use CSS if you want, but it is not recommended.

1. `resources/css`
2. `postcss.config.js`

Remove the database factory autoload mapping and facade alias from `composer.json`. Keep the `Awcodes\ClockWidget\` mapping for `src/` and the service provider registration under `extra.laravel.providers`.

The last step is to update the `package.json` file to remove unneeded options. Replace the contents of `package.json` with the following.

```json
{
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "node bin/build.js --dev",
        "build": "node bin/build.js"
    },
    "devDependencies": {
        "esbuild": "^0.17.19"
    }
}
```

Then we need to install our dependencies.

```bash
npm install
```

You may also remove the Testing directories and files, but we'll leave them in for now, although we won't be using them for this example, and we highly recommend that you write tests for your plugins.

## Step 3: Setting up the provider

Now that we have our plugin cleaned up, we can start adding our code. The boilerplate in the `src/ClockWidgetServiceProvider.php` file has a lot going on so, let's delete everything and start from scratch.

<Aside variant="info">
    Register this [asynchronous Alpine component](../advanced/assets#asynchronous-alpinejs-components) in `packageBooted()`, since it only loads where the widget is used. For assets that should load on every page of a particular panel, use [`$panel->assets()`](../panel-configuration#registering-assets-for-a-panel) in a plugin object's `register()` method instead. Registering those assets globally would load them in panels that do not use your plugin.
</Aside>

We need to be able to register our Widget with the panel and load our Alpine component when the widget is used. To do this, we'll need to add the following to the `packageBooted` method in our service provider. This will register our widget component with Livewire and our Alpine component with the Filament Asset Manager.

```php
namespace Awcodes\ClockWidget;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ClockWidgetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'clock-widget';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        Livewire::component('clock-widget', ClockWidget::class);

        // Asset Registration
        FilamentAsset::register(
            assets:[
                 AlpineComponent::make('clock-widget', __DIR__ . '/../resources/dist/clock-widget.js'),
            ],
            package: 'awcodes/clock-widget'
        );
    }
}
```

## Step 4: Create the widget

Now we can create our widget. We'll first need to extend Filament's `Widget` class in our `ClockWidget.php` file and tell it where to find the view for the widget. Since we are using the PackageServiceProvider to register our views, we can use the `::` syntax to tell Filament where to find the view.

```php
namespace Awcodes\ClockWidget;

use Filament\Widgets\Widget;

class ClockWidget extends Widget
{
    protected string $view = 'clock-widget::widget';
}
```

Next, we'll need to create the view for our widget. Create a new file at `resources/views/widget.blade.php` and add the following code. We'll make use of Filament's Blade components to save time on writing the HTML for the widget.

Use `x-load` and `x-load-src` to load the Alpine component when the widget is rendered. See [asynchronous Alpine.js components](../advanced/assets#asynchronous-alpinejs-components) for more information.

```blade
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('clock-widget::clock-widget.title') }}
        </x-slot>

        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('clock-widget', 'awcodes/clock-widget') }}"
            x-data="clockWidget()"
            class="text-center"
        >
            <p>{{ __('clock-widget::clock-widget.description') }}</p>
            <p class="text-xl" x-text="time"></p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
```

Next, we need to write our Alpine component in `src/js/index.js`. And build our assets with `npm run build`.

```js
export default function clockWidget() {
    return {
        time: new Date().toLocaleTimeString(),
        interval: null,
        init() {
            this.interval = setInterval(() => {
                this.time = new Date().toLocaleTimeString();
            }, 1000);
        },
        destroy() {
            clearInterval(this.interval);
        },
    }
}
```

Add translations for the widget text in `resources/lang/en/clock-widget.php`:

```php
return [
    'title' => 'Clock Widget',
    'description' => 'Your current time is:',
];
```

## Step 5: Update your README

You'll want to update your `README.md` file to include instructions on how to install your plugin and any other information you want to share with users, Like how to use it in their projects. For example:

```php
// Register the plugin and/or Widget in your Panel provider:

use Awcodes\ClockWidget\ClockWidget;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->widgets([
            ClockWidget::class,
        ]);
}
```

And, that's it, our users can now install our plugin and use it in their projects.
