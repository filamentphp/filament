---
title: Getting started
---
import Aside from "@components/Aside.astro"

Once you have [installed Filament](introduction/installation#installing-the-panel-builder), you can start building your application.

<Aside variant="info">
    This guide is for the Filament panel builder. If you are looking to use the Filament UI components outside of a panel, visit the [Components](components) documentation.
</Aside>

To start, visit `/admin` and sign in with a user account. You will be redirected to the default dashboard of the panel.

## Resources

Resources are the core of your Filament application. They are CRUD UIs for models that you want to manage in the panel.

Out of the box, Filament will generate three pages in each resource:
- **List**: A paginated table of all the records in the Eloquent model.
- **Create**: A form to create a new record.
- **Edit**: A form to edit an existing record.

You can also choose to generate a **View** page, which is a read-only display of a record.

Each resource usually has an item in the sidebar, which is automatically registered as soon as you create a resource.

To start your journey by creating a resource, visit the [Resources documentation](resources).

## Widgets

Widgets are components often used to build a dashboard, typically to display statistical data. Charts, numbers, tables, and completely custom widgets are supported.

Each widget has a PHP class and a Blade view. The PHP class is technically a [Livewire component](https://livewire.laravel.com/docs/components). As such, every widget has access to the full power of Livewire to build an interactive server-rendered UI.

Out of the box, Filament's dashboard contains a couple of widgets: one which greets the user and allows them to sign out, and another which displays information about Filament.

To start your journey by adding your own widget to the dashboard, visit the [Widgets documentation](widgets).

## Custom pages

Custom pages are a blank canvas for you to build whatever you want in a panel. They are often used for settings pages, documentation, or anything else you can think of.

Each custom page has a PHP class and a Blade view. The PHP class is technically a [full-page Livewire component](https://livewire.laravel.com/docs/components) (in fact, every page class in a Filament panel is a Livewire component). As such, every page has access to the full power of Livewire to build an interactive server-rendered UI.

To start your journey by creating a custom page, visit the [Custom pages documentation](navigation/custom-pages).
