---
title: Getting started
---

## Introduction

While Filament comes with virtually any tool you'll need to build great apps, sometimes you'll need to add your own functionality either for just your app or as redistributable packages that other developers can include in their own apps. This is why Filament offers a plugin system that allows you to extend its functionality.

## Creating a plugin

While you can certainly build plugins from scratch, we recommend using the [Filament Plugin Skeleton](https://github.com/filamentphp/plugin-skeleton) to quickly get started. This skeleton includes all the necessary boilerplate to get you up and running quickly.

### Usage

To use the skeleton, go to the GitHub repository and click the "Use this template" button. This creates a new repository in your account with the skeleton code. Clone the repository to your machine, navigate to its root directory, and run:

```bash
php ./configure.php
```

The command asks you a series of questions, then configures the new plugin so you can begin building it.

## Understanding plugin types

Plugins can be used in two main contexts:

1. **Panel Plugins**: These are plugins that are used with [Panel Builders](../introduction/installation). They are typically used only to add functionality when used inside a Panel or as a complete Panel in and of itself. Examples of this are:
   1. A plugin that adds specific functionality to the dashboard in the form of Widgets.
   2. A plugin that adds a set of Resources / functionality to an app like a Blog or User Management feature.
2. **Standalone Plugins**: These are plugins that are used in any context outside a Panel Builder. Examples of this are:
   1. A plugin that adds custom fields to be used with the [Form Builders](../forms/overview).
   2. A plugin that adds custom columns or filters to the [Table Builders](../tables/overview).

Although these are two different mental contexts to keep in mind when building plugins, they can be used together inside the same plugin. They do not have to be mutually exclusive.

## Important concepts

Before we dive into the specifics of building plugins, there are a few concepts that are important to understand. You should familiarize yourself with the following before building a plugin:

1. [Laravel Package Development](https://laravel.com/docs/packages)
2. [Spatie Package Tools](https://github.com/spatie/laravel-package-tools)
3. [Filament Asset Management](../advanced/assets)

### The Plugin object

Filament introduces the concept of a Plugin object that is used to configure the plugin. This object is a simple PHP class that implements the `Filament\Contracts\Plugin` interface. This class is used to configure the plugin and is the main entry point for the plugin. It is also used to register Resources and Icons that might be used by your plugin.

While the plugin object is extremely helpful, it is not required to build a plugin. You can still build plugins without using the plugin object as you can see in the [building a panel plugin](building-a-panel-plugin) tutorial.

<Aside variant="info">
   The Plugin object is only used for Panel Providers. Standalone Plugins do not use this object. All configuration for Standalone Plugins should be handled in the plugin's service provider.
</Aside>

### Registering assets

Register [assets](../advanced/assets) shared by standalone components, including lazily loaded CSS, JavaScript, and Alpine components, in the plugin service provider's `packageBooted()` method. For assets that should load on every page of a particular panel, use [`$panel->assets()`](../panel-configuration#registering-assets-for-a-panel) in the plugin object's `register()` method. This avoids loading panel-specific assets in panels that do not use your plugin.

## Upgrading existing plugins

To update a plugin from Filament 3 to 4, follow the [upgrade guide](../upgrade-guide) for the packages your plugin uses. Update its Composer constraints and test it in a Filament 4 application, including any custom views, styles, and JavaScript.

### Helpful links

Please read this guide in its entirety before upgrading your plugin. It will help you understand the concepts and how to build your plugin.

1. [Filament Asset Management](../advanced/assets)
2. [Panel Plugin Development](panel-plugins)
3. [Icon Management](../styling/icons)
4. [Colors Management](../styling/colors)
5. [CSS Hooks](../styling/css-hooks)
