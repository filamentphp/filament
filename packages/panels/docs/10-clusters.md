---
title: Clusters
---

## Overview

Clusters are a hierarchical structure in panels that allow you to group [resources](resources) and [custom pages](pages) together. They are useful for organizing your panel into logical sections, and can help reduce the size of your panel's sidebar.

When using a cluster, a few things happen:

- A new navigation item is added to the navigation, which is a link to the first resource or page in the cluster.
- The individual navigation items for the resources or pages are no longer visible in the main navigation.
- A new sub-navigation UI is added to each resource or page in the cluster, which contains the navigation items for the resources or pages in the cluster.
- Resources and pages in the cluster get a new URL, prefixed with the name of the cluster. If you are generating URLs to [resources](resources/getting-started#generating-urls-to-resource-pages) and [pages](pages#generating-urls-to-pages) correctly, then this change should be handled for you automatically.
- The cluster's name is in the breadcrumbs of all resources and pages in the cluster. When clicking it, you are taken to the first resource or page in the cluster.

## Creating a cluster

Before creating your first cluster, you must tell the panel where cluster classes should be located. Alongside methods like `discoverResources()` and `discoverPages()` in the [configuration](configuration), you can use `discoverClusters()`:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters');
}
```

Now, you can create a cluster with the `php artisan make:filament-cluster` command:

```bash
php artisan make:filament-cluster Settings
```

This will create a new cluster class in the `app/Filament/Clusters` directory:

```php
<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
}
```

The [`$navigationIcon`](navigation#customizing-a-navigation-items-icon) property is defined by default since you will most likely want to customize this immediately. All other [navigation properties and methods](navigation) are also available to use, including [`$navigationLabel`](navigation#customizing-a-navigation-items-label), [`$navigationSort`](navigation#sorting-navigation-items) and [`$navigationGroup`](navigation#grouping-navigation-items). These are used to customize the cluster's main navigation item, in the same way you would customize the item for a resource or page.

## Adding resources and pages to a cluster

To add resources and pages to a cluster, you just need to define the `$cluster` property on the resource or page class, and set it to the cluster class [you created](#creating-a-cluster):

```php
use App\Filament\Clusters\Settings;

protected static ?string $cluster = Settings::class;
```

## Code structure recommendations for panels using clusters

When using clusters, it is recommended that you move all of your resources and pages into a directory with the same name as the cluster. For example, here is a directory structure for a panel that uses a cluster called `Settings`, containing a `ColorResource` and two custom pages:

```
.
+-- Clusters
|   +-- Settings.php
|   +-- Settings
|   |   +-- Pages
|   |   |   +-- ManageBranding.php
|   |   |   +-- ManageNotifications.php
|   |   +-- Resources
|   |   |   +-- ColorResource.php
|   |   |   +-- ColorResource
|   |   |   |   +-- Pages
|   |   |   |   |   +-- CreateColor.php
|   |   |   |   |   +-- EditColor.php
|   |   |   |   |   +-- ListColors.php
```

This is a recommendation, not a requirement. You can structure your panel however you like, as long as the resources and pages in your cluster use the [`$cluster`](#adding-resources-and-pages-to-a-cluster) property. This is just a suggestion to help you keep your panel organized.

When a cluster exists in your panel, and you generate new resources or pages with the `make:filament-resource` or `make:filament-page` commands, you will be asked if you want to create them inside a cluster directory, according to these guidelines. If you choose to, then Filament will also assign the correct `$cluster` property to the resource or page class for you. If you do not, you will need to [define the `$cluster` property](#adding-resources-and-pages-to-a-cluster) yourself.
