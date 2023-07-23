---
title: Getting started
---

## Overview

While Filament comes with virtually any tool you'll need to build great apps, sometimes you'll need to add your own functionality either for just your app or as redistributable packages that other developers can include in their own apps. This is why Filament offers a plugin system that allows you to extend its functionality.

Before we dive in, it's important to understand the different contexts in which plugins can be used. There are two main contexts:

1. **Panel Plugins**: These are plugins that are used with [Panel Builders](/docs/3.x/panels/installation). They are typically used only to add functionality when used inside a Panel or as a complete Panel in and of itself. Examples of this are:
   1. A plugin that adds specific functionality to the dashboard in the form of Widgets.
   2. A plugin that adds a set of Resources / functionality to an app like a Blog or User Management feature.
2. **Standalone Plugins**: These are plugins that are used in any context outside a Panel Builder. Examples of this are:
   1. A plugin that adds custom fields to be used with the [Form Builders](/docs/3.x/forms/installation/).
   2. A plugin that adds custom columns or filters to the [Table Builders](/docs/3.x/tables/installation/).

Although these are two different mental contexts to keep in mind when building plugins they can be used together inside the same plugin. They do not have to be mutually exclusive.

## Important Concepts

Before we dive into the specifics of building plugins, there are a few concepts that are important to understand. You should familiarize yourself with the following before building a plugin:

1. [Laravel Package Development](https://laravel.com/docs/10.x/packages)
2. [Spatie Package Tools](https://github.com/spatie/laravel-package-tools)
3. [Filament Asset Management](/docs/3.x/support/assets)

## Creating a Plugin

While you can certainly build plugins from scratch, we recommend using the [Filament Plugin Skeleton](https://github.com/filamentphp/plugin-skeleton) to quickly get started. This skeleton includes all the necessary boilerplate to get you up and running quickly.

### Usage

To use the skeleton, simply go to the GitHub repo and click the "Use this template" button (***While Filament is in v3 Beta make sure you switch to the 3.x branch before using the template***). This will create a new repo in your account with the skeleton code. After that you can clone the repo to your machine. Once you have the code on your machine, navigate to the root of the project and run the following command:

```bash
php ./configure.php
```

This will ask you a series of questions to configure the plugin. Once you've answered all the questions the script will stub out a new plugin for you and you can begin to build your amazing new extension for Filament.
