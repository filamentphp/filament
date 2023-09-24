---
title: Build a panel plugin
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Panel Builder Plugins"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to get started with your plugin. The text-based guide on this page can also give a good overview."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/16"
/>

## Preface

Please read the docs on [panel plugin development](/docs/3.x/panels/plugins) and the [getting started guide](/docs/3.x/support/plugins/getting-started) before continuing.

## Overview

In this walkthrough, we'll build a simple plugin that adds a new form field that can be used in forms. This also means it will be available to users in their panels.

You can find the final code for this plugin at [https://github.com/awcodes/clock-widget](https://github.com/awcodes/clock-widget).

## Step 1: Create the plugin

First, we'll create the plugin using the steps outlined in the [getting started guide](/docs/3.x/support/plugins/getting-started#creating-a-plugin).

## Step 2: Clean Up

Next, we'll clean up the plugin to remove the boilerplate code we don't need. This will seem like a lot, but since this is a simple plugin, we can remove a lot of the boilerplate code.

Remove the following directories and files:
1. `config`
1. `database`
1. `src/Commands`
1. `src/Facades`
1. `stubs`

Since our plugin doesn't have any settings or additional methods needed for functionality, we can also remove the `ClockWidgetPlugin.php` file.

1. `ClockWidgetPlugin.php`

Since Filament v3 recommends that users style their plugins with a custom filament theme, we'll remove the files needed for using css in the plugin. This is optional, and you can still use css if you want, but it is not recommended.

1. `resources/css`
1. `postcss.config.js`
1. `tailwind.config.js`

Now we can clean up our `composer.json` file to remove unneeded options.

```json
"autoload": {
    "psr-4": {
        // We can remove the database factories
        "Awcodes\\ClockWidget\\Database\\Factories\\": "database/factories/"
    }
},
"extra": {
    "laravel": {
        // We can remove the facade
        "aliases": {
            "ClockWidget": "Awcodes\\ClockWidget\\Facades\\ClockWidget"
        }
    }
},
```

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

## Step 3: Setting up the Provider

Now that we have our plugin cleaned up, we can start adding our code. The boilerplate in the `src/ClockWidgetServiceProvider.php` file has a lot going on so, let's delete everything and start from scratch.

We need to be able to register our Widget with the panel and load our Alpine component when the widget is used. To do this, we'll need to add the following to the `packageBooted` method in our service provider. This will register our widget component with Livewire and our Alpine component with the Filament Asset Manager.

```php
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

## Step 4: Create the Widget

Now we can create our widget. We'll first need to extend Filament's `Widget` class in our `ClockWidget.php` file and tell it where to find the view for the widget. Since we are using the PackageServiceProvider to register our views, we can use the `::` syntax to tell Filament where to find the view.

```php
use Filament\Widgets\Widget;

class ClockWidget extends Widget
{
    protected static string $view = 'clock-widget::widget';
}
```

Next, we'll need to create the view for our widget. Create a new file at `resources/views/widget.blade.php` and add the following code. We'll make use of Filament's blade components to save time on writing the html for the widget.

We are using async Alpine to load our Alpine component, so we'll need to add the `x-ignore` attribute to the div that will load our component. We'll also need to add the `ax-load` attribute to the div to tell Alpine to load our component. You can learn more about this in the [Core Concepts](/docs/3.x/support/assets#asynchronous-alpinejs-components) section of the docs.

```blade
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('clock-widget::clock-widget.title') }}
        </x-slot>

        <div
            x-ignore
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('clock-widget', 'awcodes/clock-widget') }}"
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
        init() {
            setInterval(() => {
                this.time = new Date().toLocaleTimeString();
            }, 1000);
        }
    }
}
```

We should also add translations for the text in the widget so users can translate the widget into their language. We'll add the translations to `resources/lang/en/widget.php`.

```php
return [
    'title' => 'Clock Widget',
    'description' => 'Your current time is:',
];
```

## Step 5: Update your Readme

You'll want to update your `README.md` file to include instructions on how to install your plugin and any other information you want to share with users, Like how to use it in their projects. For example:

```php
Register the plugin and/or Widget in your Panel provider:

use Awcodes\ClockWidget\ClockWidgetWidget;

public function panel(Panel $panel): Panel
{
    return $panel
        ->widgets([
            ClockWidgetWidget::class,
        ]);
}
```

And, that's it, our users can now install our plugin and use it in their projects.
