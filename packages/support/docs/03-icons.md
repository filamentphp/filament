---
title: Icons
---

## Overview

Icons are used throughout the entire Filament UI to visually communicate core parts of the user experience. To render icons, we use the [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package from Blade UI Kit.

They have a website where you can [search all the available icons](https://blade-ui-kit.com/blade-icons?set=1#search) from various Blade Icons packages. Each package contains a different icon set that you can choose from.

## Using custom SVGs as icons

The [Blade Icons] package allows you to register custom SVGs as icons. This is useful if you want to use your own custom icons in Filament.

To start with, publish the Blade Icons configuration file:

```bash
php artisan vendor:publish --tag=blade-icons
```

Now, open the `config/blade-icons.php` file, and uncomment the `default` set in the `sets` array.

Now that the default set exists in the config file, you can simply put any icons you want inside the `resources/svg` directory of your application. For example, if you put an SVG file named `star.svg` inside the `resources/svg` directory, you can reference it anywhere in Filament as `icon-star`. The `icon-` prefix is configurable in the `config/blade-icons.php` file too. You can also render the custom icon in a Blade view using the [`@svg('icon-star')` directive](https://github.com/blade-ui-kit/blade-icons#directive).

## Replacing the default icons

Filament includes an icon management system that allows you to replace any icons are used by default in the UI with your own. This happens in the `boot()` method of any service provider, like `AppServiceProvider`, or even a dedicated service provider for icons. If you wanted to build a plugin to replace Heroicons with a different set, you could absolutely do that by creating a Laravel package with a similar service provider.

To replace an icon, you can use the `FilamentIcon` facade. It has a `register()` method, which accepts an array of icons to replace. The key of the array is the unique [icon alias](#available-icon-aliases) that identifies the icon in the Filament UI, and the value is name of a Blade icon to replace it instead:

```php
use Filament\Support\Facades\FilamentIcon;

FilamentIcon::register([
    'panels::topbar.global-search.field' => 'fas-magnifying-glass',
    'panels::sidebar.group.collapse-button' => 'fas-chevron-up',
]);
```

### Allowing users to customize icons from your plugin

If you have built a Filament plugin, your users may want to be able to customize icons in the same way that they can with any core Filament package. This is possible if you replace any manual `@svg()` usages with the `<x-filament::icon>` Blade component. This component allows you to pass in an icon alias, the name of the SVG icon that should be used by default, and any classes or HTML attributes:

```blade
<x-filament::icon
    name="heroicon-m-magnifying-glass"
    alias="panels::topbar.global-search.field"
    wire:target="search"
    class="h-5 w-5 text-gray-500 dark:text-gray-400"
/>
```

## Available icon aliases

### Panel Builder icon aliases

- `panels::pages.dashboard.navigation-item` - Dashboard navigation item
- `panels::pages.tenancy.register-tenant.open-tenant-button` - Button to open a tenant from the tenant registration page
- `panels::sidebar.collapse-button` - Desktop sidebar collapse button when it is partially collapsible
- `panels::sidebar.collapse-button.full` - Desktop sidebar collapse button when it is fully collapsible
- `panels::sidebar.group.collapse-button` - Collapse button for a sidebar group
- `panels::topbar.global-search.field` - Global search field
- `panels::topbar.close-mobile-sidebar-button` - Button to close the mobile sidebar
- `panels::topbar.open-mobile-sidebar-button` - Button to open the mobile sidebar
- `panels::topbar.open-database-notifications-button` - Button to open the database notifications modal
- `panels::topbar.user-menu.profile-item` - Profile item in the user menu
- `panels::topbar.user-menu.logout-button` - Button in the user menu to log out
- `panels::topbar.user-menu.theme-switcher.light-button` - Button in the user menu to switch to the light theme
- `panels::topbar.user-menu.theme-switcher.dark-button` - Button in the user menu to switch to the dark theme
- `panels::topbar.user-menu.theme-switcher.system-button` - Button in the user menu to switch to the system theme
- `panels::widgets.account.logout-button` - Button in the account widget to log out

### Form Builder icon aliases

- `forms:components.checkbox-list.search-field` - Search input in a checkbox list
- `forms::components.tags-input.delete-button` - Button to delete a tag in a tags input
- `forms::components.wizard.completed-step` - Completed step in a wizard

### Table Builder icon aliases

- `tables::columns.collapse-button`
- `tables::filters.remove-button` - Button to remove a filter
- `tables::filters.remove-all-button` - Button to remove all filters
- `tables::grouping.collapse-button` - Button to collapse a group of records
- `tables::header-cell.sort-asc` - Sort button of a column sorted in ascending order
- `tables::header-cell.sort-desc` - Sort button of a column sorted in descending order
- `tables::pagination.previous-button` - Button to go to the previous page, used on mobile and by "simple pagination"
- `tables::pagination.next-button` - Button to go to the next page, used on mobile and by "simple pagination"
- `tables::reorder.handle` - Handle to grab in order to reorder a record with drag and drop
- `tables::search-field` - Search input

### Notifications icon aliases

- `notifications::database.modal.empty-state` - Empty state of the database notifications modal
- `notifications::database.modal.pagination.previous-button` - Button to go to the previous page of the database notifications modal
- `notifications::database.modal.pagination.next-button` - Button to go to the next page of the database notifications modal
- `notifications::notification.close-button` - Button to close a notification

### UI components icon aliases

- `badge.delete-button` - Button to delete a badge
- `breadcrumbs.separator` - Separator between breadcrumbs
- `modal.close-button` - Button to close a modal
- `section.collapse-button` - Button to collapse a section
