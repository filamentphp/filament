---
title: Overview
---

## What is a panel?

In Filament, a panel is a modular section of your Laravel application that groups resources (CRUD interfaces), dashboards, and any custom pages you create. Panels help organize and manage different parts of your application, such as an admin panel, a user dashboard in a SaaS, or any other distinct area in your frontend. Each panel can have its own configuration for branding, navigation, and routing, making it easier to build and maintain complex applications.

Panels are registered in their own Laravel service provider class, which is also used to configure it. This makes it easy to add many independent panels to one Laravel installation without much complexity. Panels can even be registered within Laravel packages, making them easy to share and reuse across different projects. Panels are Livewire applications, but since they just use the standard Laravel routing system, they can live alongside any other frontend that you serve from your Laravel app, such as using Inertia.js.

## Why are there other Filament packages, such as forms and tables?

When Filament was originally released, the only option was to use a panel. The panels package contained all the functionality needed to build CRUD interfaces, including a form and table builder. However, as Filament has grown, it has become clear that many developers want to use the form and table builders, alongside other Filament components, in Blade applications that they've built from scratch. As a result, the form and table builders were extracted into their own packages, so they can be installed and used independently of a panel. All the features that you find in these packages may be used inside a panel, but they can also be used in any other Blade application.

## Should I use a panel or build my own Blade application?

If you're building a new application from scratch, you should consider using a panel. Panels are a great way to organize your application and make it easier to build and maintain. They provide a lot of functionality out of the box, such as authentication, navigation, and automatic routing. However, if you're working on an existing application, or you have specific requirements that don't fit into the panel model, including a unique UI layout or routing structure, you may want to build your own Blade application. The form and table builders, as well as other Filament components, can be used in any Blade application, so you can still take advantage of the functionality that Filament provides.

## How does a panel work?

Apart from a few exceptions, each page in a panel is defined by a PHP class that lives in the filesystem of your project. These PHP classes extend the `Filament\Pages\Page` class, which is a type of Livewire component. Each panel has an index of all the page classes for that panel, and automatically registers a Laravel `GET` route to render them. To index pages in a panel, you can inform the panel of which directories to scan to find the corresponding files, as well as passing a list of page classes outside those directories to register additionally. For example, a panel configuration may contain the following lines:

```php
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ]);
    }
}
```

The `discoverPages()` method registers a directory and namespace for page auto-discovery, in this case `app/Filament/Pages`. Any page classes found in the directory will be registered with the panel. The `pages()` method registers additional page classes that are not found in the directories passed to `discoverPages()`, in this case the `Dashboard` class lives somewhere inside the `/vendor` folder in the `Filament\Pages` namespace, so is not auto-discovered. You can call `discoverPages()` multiple times to register multiple directories for page auto-discovery.

### How do CRUD resources differ from pages?

Often, CRUD interfaces are the backbone of a panel. If you aren't aware, CRUD stands for "create", "read", "update", and "delete", which are common operations that you may perform on a table in your database. Filament represents these operations as UI pages grouped by a "resource". A resource wires together these pages by associating them with a particular Eloquent model class that exists in your project. By default, when creating a resource for a panel, the following files are created in the `app/Filament/Resources` directory:

```
+-- Customers
|   +-- CustomerResource.php
|   +-- Pages
|   |   +-- CreateCustomer.php
|   |   +-- EditCustomer.php
|   |   +-- ListCustomers.php
```

- The `CustomerResource` class is the main resource file, where you can define the Eloquent model, customize the labeling for the UI, register and customize a navigation item that links to the CRUD, and any other configuration associated with it. This class extends `Filament\Resources\Resource`.
- The `Pages` directory contains the page classes that belong to that resource. These page classes are not auto-discovered by the panel since they live inside `app/Filament/Resources`, not `app/Filament/Pages`. They are registered in the main `CustomerResource.php` file, which is then auto-discovered or registered by the panel.
  - The `ListCustomers` class is a page class that inherits from the `Filament\Resources\Pages\ListRecords` class, which itself extends `Filament\Pages\Page` and as such is a Livewire component. By default, it serves as a table view of all the records in the database table associated with the resource's Eloquent model. Out of the box, there is a way to select multiple records in the table and delete them all at once through a button and confirmation modal. It is rendered to the `index` route of the resource, which typically has a URL like `/customers`.
  - The `CreateCustomer` class extends `Filament\Resources\Pages\CreateRecord`, which renders a form to create a new record for the resource's Eloquent model. Once the form is submitted, the resource will handle the creation of the record in the database. It is rendered to the `create` route of the resource, which typically has a URL like `/customers/create`.
  - The `EditCustomer` class extends `Filament\Resources\Pages\EditRecord`, which renders a form to edit an existing record for the resource's Eloquent model. The resource is responsible for loading the existing data into the form from the database and saving any updates to the database once the form is submitted. By default, there is a "delete" button on this page which opens a confirmation modal before eventually deleting the record from the database. It is rendered to the `edit` route of the resource, which typically has a URL like `/customers/{record}/edit`, where `{record}` is the primary key of the record being edited.

Although these are the default pages created when generating a resource, you are not limited to them. You may create additional "custom" pages and register them in the resource, which handles their routing.

Often, a project will require a read-only page to view a single record if some users are unable to edit them, or the read-only interface requires an enhanced display with extra data. Filament's "infolists" feature can be used to create these sorts of pages. You may add a `ViewCustomer` page to the resource, which extends `Filament\Resources\Pages\ViewRecord`, and is rendered to the `view` route of the resource, which typically has a URL like `/customers/{record}`, where `{record}` is the primary key of the record being viewed. On this page, an infolist is used to display the record's data in a read-only format. If an infolist is not defined, the form from the create and edit pages is reused in a "disabled" state to display the record's data in a read-only format. This can be suitable in some cases where extra information is not required and the form layout is acceptable. It can also serve as a quick way to get started with the view page, and you may always define a dedicated infolist later.

There is also an alternative form of resource, nicknamed a "simple" resource, where one or more of the pages can be replaced with modal operations on the list page. When defining a "simple" resource, the `ListCustomers` page is replaced with `ManageCustomers` page, which extends `Filament\Resources\Pages\ManageRecords` to tweak the UI a little for this use case. When using a simple resource, you could choose to make the following changes to the interface:

- The `CreateCustomer` page can be removed, and the link to create a new customer on the `ManageCustomers` page can open a form in a modal instead. This modal has a button in its footer to submit the form and create the record in the database and re-render the table with the new row.
- The `EditCustomer` page can be removed, and the edit button on each row of the `ManageCustomers` table can open a form in a modal instead. This modal has a button in its footer to submit the form and update the record in the database and re-render the table with the updated data.
- An individual delete button can be added to each row of the `ManageCustomers` table, alongside the buttons to select and bulk delete multiple records at once.
- If your resource requires a read-only interface to view a singular record, you may use a view button on each row of the `ManageCustomers` table to open a modal with the same infolist or disabled form, instead of an additional `ViewCustomer` page.

As mentioned previously, pages inside a resource are not auto-discovered by a panel, and they are registered in the resource class. The resource is then auto-discovered or registered by the panel. The registration of resources is very similar to the registration of pages that don't belong to a resource, as shown in the following example:

```php
use DanHarrin\FilamentUsers\Resources\Users\UserResource;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                UserResource::class,
            ]);
    }
}
```

In this example, any resource classes found in the `app/Filament/Resources` directory will be registered with the panel. The example `CustomerResource` class mentioned earlier could be registered in this way. Additionally, the `UserResource` class is registered, which is not auto-discovered because it lives in the `/vendor` folder in the `DanHarrin\FilamentUsers\Resources` namespace.

### How do dashboard widgets work?

A dashboard is a type of page that is used to display "widgets". Widgets are individual Livewire components that extend `Filament\Widgets\Widget`, and are often used to display data in a visual format, such as a chart or a list of statistics. Each widget is represented by a PHP class in your project, in the same way that pages are. They can be discovered by a panel similarly:

```php
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ]);
    }
}
```

The `discoverWidgets()` method registers a directory and namespace for widget auto-discovery, in this case `app/Filament/Widgets`. Any widget classes found in the directory will be registered with the panel. The `widgets()` method registers additional widget classes that are not found in the directories passed to `discoverWidgets()`, in this case the `AccountWidget` and `FilamentInfoWidget` classes live somewhere inside the `/vendor` folder in the `Filament\Widgets` namespace, so they are not auto-discovered. You can call `discoverWidgets()` multiple times to register multiple directories for widget auto-discovery.

### What happens if I have multiple panels with different pages?

By default, a panel will register pages, resources and widgets like so:

```php
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets');
    }
}
```

However, what happens if you have another panel registered and you need a completely different set of resources, pages, and widgets? You can just isolate all these files into their own folder, and discover them inside that. For example, you may need a "client" panel, so you create `app/Filament/Client/Resources`, `app/Filament/Client/Pages`, and `app/Filament/Client/Widgets` directories. You can then register these directories in the `ClientPanelProvider` class:

```php
use Filament\Panel;
use Filament\PanelProvider;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets');
    }
}
```

You could even move all of your "admin" files into one `app/Filament/Admin` directory and update the paths in the `AdminPanelProvider` class to reflect this change. This way, you can keep all your panel files organized and separate from each other.
