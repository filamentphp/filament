---
title: Getting started
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Setting up a Plugin"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to get started with your plugin. The text-based guide on this page can also give a good overview."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/12"
/>

## Overview

While Filament comes with virtually any tool you'll need to build great apps, sometimes you'll need to add your own functionality either for just your app or as redistributable packages that other developers can include in their own apps. This is why Filament offers a plugin system that allows you to extend its functionality.

Before we dive in, it's important to understand the different contexts in which plugins can be used. There are two main contexts:

1. **Panel Plugins**: These are plugins that are used with [Panel Builders](/docs/3.x/panels/installation). They are typically used only to add functionality when used inside a Panel or as a complete Panel in and of itself. Examples of this are:
   1. A plugin that adds specific functionality to the dashboard in the form of Widgets.
   2. A plugin that adds a set of Resources / functionality to an app like a Blog or User Management feature.
2. **Standalone Plugins**: These are plugins that are used in any context outside a Panel Builder. Examples of this are:
   1. A plugin that adds custom fields to be used with the [Form Builders](/docs/3.x/forms/installation/).
   2. A plugin that adds custom columns or filters to the [Table Builders](/docs/3.x/tables/installation/).

Although these are two different mental contexts to keep in mind when building plugins, they can be used together inside the same plugin. They do not have to be mutually exclusive.

## Important Concepts

Before we dive into the specifics of building plugins, there are a few concepts that are important to understand. You should familiarize yourself with the following before building a plugin:

1. [Laravel Package Development](https://laravel.com/docs/packages)
2. [Spatie Package Tools](https://github.com/spatie/laravel-package-tools)
3. [Filament Asset Management](/docs/3.x/support/assets)

### The Plugin object

Filament v3 introduces the concept of a Plugin object that is used to configure the plugin. This object is a simple PHP class that extends the `Filament\Support\Plugin` class. This class is used to configure the plugin and is the main entry point for the plugin. It is also used to register Resources and Icons that might be used by your plugin.

While the plugin object is extremely helpful, it is not required to build a plugin. You can still build plugins without using the plugin object as you can see in the [Building a Panel Plugin](/docs/3.x/support/plugins/build-a-panel-plugin) tutorial.

> **Info** 
> The Plugin object is only used for Panel Providers. Standalone Plugins do not use this object. All configuration for Standalone Plugins should be handled in the plugin's service provider.

### Registering Assets

All [asset registration](/docs/3.x/support/assets), including CSS, JS and Alpine Components, should be done through the plugin's service provider in the `packageBooted()` method. This allows Filament to register the assets with the Asset Manager and load them when needed.

## Creating a Plugin

While you can certainly build plugins from scratch, we recommend using the [Filament Plugin Skeleton](https://github.com/filamentphp/plugin-skeleton) to quickly get started. This skeleton includes all the necessary boilerplate to get you up and running quickly.

### Usage

To use the skeleton, simply go to the GitHub repo and click the "Use this template" button. This will create a new repo in your account with the skeleton code. After that, you can clone the repo to your machine. Once you have the code on your machine, navigate to the root of the project and run the following command:

```bash
php ./configure.php
```

This will ask you a series of questions to configure the plugin. Once you've answered all the questions, the script will stub out a new plugin for you, and you can begin to build your amazing new extension for Filament.

## Upgrading existing plugins

Since every plugin varies greatly in its scope of use and functionality, there is no one size fits all approaches to upgrading existing plugins. However, one thing to note, that is consistent to all plugins is the deprecation of the `PluginServiceProvider`.

In your plugin service provider, you will need to change it to extend the PackageServiceProvider instead. You will also need to add a static `$name` property to the service provider. This property is used to register the plugin with Filament. Here is an example of what your service provider might look like:

```php
class MyPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'my-plugin';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
```

### Helpful links

Please read this guide in its entirety before upgrading your plugin. It will help you understand the concepts and how to build your plugin.

1. [Filament Asset Management](/docs/3.x/support/assets)
2. [Panel Plugin Development](/docs/3.x/panels/plugins)
3. [Icon Management](/docs/3.x/support/icons)
4. [Colors Management](/docs/3.x/support/colors)
5. [Style Customization](/docs/3.x/support/style-customization)
