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

To replace an icon, you can use the `FilamentIcon` facade. It has a `register()` method, which accepts an array of icons to replace. The key of the array is the unique [icon alias](#available-icon-aliases) that identifies the icon in the Filament UI, and the value is an `Icon` object that represents the icon that should be used instead:

```php
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Icon;

FilamentIcon::register([
    'panels::topbar.global-search.input' => Icon::make('fas-magnifying-glass'),
    'panels::sidebar.group.collapse-button' => Icon::make('fas-chevron-up'),
]);
```

### Customizing the size of icons

By default, Filament will use [Tailwind classes](https://tailwindcss.com/docs/height) to set the size of icons. To customize the size of an icon you have registered, you can use the `size()` method of the `Icon` object, and pass in a set of CSS classes to use instead:

```php
use Filament\Support\Icons\Icon;

Icon::make('fas-magnifying-glass')
    ->size('h-6 w-6')
```

### Customizing the color of icons

By default, Filament will use [Tailwind classes](https://tailwindcss.com/docs/text-color) to set the color of icons. To customize the color of an icon you have registered, you can use the `color()` method of the `Icon` object, and pass in a set of CSS classes to use instead:

```php
use Filament\Support\Icons\Icon;

Icon::make('fas-magnifying-glass')
    ->color('text-gray-600 dark:text-gray-500')
```

### Adding additional classes to icons

You can use the `class()` method of the `Icon` object, and pass in a set of additional CSS classes to add to the SVG:

```php
use Filament\Support\Icons\Icon;

Icon::make('fas-magnifying-glass')
    ->class('global-search-input-icon')
```

### Customizing the appearance of icons without changing the SVG

When registering icons using `FilamentIcon::register()`, you don't need to pass the name of a new icon to use. By using an `Icon` object without a name, you can customize the [size](#customizing-the-size-of-icons), [color](#customizing-the-color-of-icons), and [additional classes](#adding-additional-classes-to-icons) of an icon without changing the SVG:

```php
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Icon;

FilamentIcon::register([
    'panels::topbar.global-search.input' => Icon::make()
        ->size('h-6 w-6')
        ->color('text-gray-600 dark:text-gray-500')
        ->class('global-search-input-icon'),
]);
```

### Allowing users to customize icons from your plugin

If you have built a Filament plugin, your users may want to be able to customize icons in the same way that they can with any core Filament package. This is possible if you replace any manual `@svg()` usages with the `<x-filament::icon>` Blade component. This component allows you to pass in an icon alias, the name of the SVG icon that should be used by default, default size classes, default color classes, and additional classes or HTML attributes:

```blade
<x-filament::icon
    name="heroicon-m-magnifying-glass"
    alias="panels::topbar.global-search.input"
    color="text-gray-500 dark:text-gray-400"
    size="h-5 w-5"
    wire:target="search"
/>
```

## Available icon aliases

### Panel Builder icon aliases

- `panels::pages.dashboard.navigation-item` - Dashboard navigation item
- `panels::pages.tenancy.register-tenant.open-tenant-button` - Button to open a tenant from the tenant registration page
- `panels::sidebar.collapse-button` - Desktop sidebar collapse button when it is partially collapsible
- `panels::sidebar.collapse-button.full` - Desktop sidebar collapse button when it is fully collapsible
- `panels::sidebar.group` - Sidebar group
- `panels::sidebar.group.collapse-button` - Collapse button for a sidebar group
- `panels::sidebar.item` - Sidebar item
- `panels::topbar.global-search.input` - Global search input
- `panels::topbar.item` - Top navigation item
- `panels::topbar.open-mobile-sidebar-button` - Button to open the mobile sidebar
- `panels::topbar.open-database-notifications-button` - Button to open the database notifications modal
- `panels::topbar.user-menu.theme-switcher.light-button` - Button in the user menu to switch to the light theme
- `panels::topbar.user-menu.theme-switcher.dark-button` - Button in the user menu to switch to the dark theme
- `panels::topbar.user-menu.theme-switcher.system-button` - Button in the user menu to switch to the system theme

### Form Builder icon aliases

- `forms::components.affixes.prefix` - Before a field's input area
- `forms::components.affixes.suffix` - After a field's input area
- `forms::components.date-time-picker` - Date-time picker input
- `forms::components.hint` - Aside a field's hint
- `forms::components.tags-input.delete-button` - Button to delete a tag in a tags input
- `forms::components.toggle.off` - Toggle in the off position
- `forms::components.toggle.on` - Toggle in the on position
- `forms::components.wizard.completed-step` - Completed step in a wizard
- `forms::components.wizard.current-step` - Current step in a wizard

### Table Builder icon aliases

- `tables::columns.collapse-button`
- `tables::columns.icon` - Icon column
- `tables::columns.summaries.icon-count` - `Count` summariser of icons in a column
- `tables::columns.text` - Aside a text column's textual content
- `tables::columns.toggle.off` - Toggle column in the off position
- `tables::columns.toggle.on` - Toggle column in the on position
- `tables::empty-state` - Empty state
- `tables::filters.remove-button` - Button to remove a filter
- `tables::filters.remove-all-button` - Button to remove all filters
- `tables::grouping.collapse-button` - Button to collapse a group of records
- `tables::header-cell.sort-asc` - Sort button of a column sorted in ascending order
- `tables::header-cell.sort-desc` - Sort button of a column sorted in descending order
- `tables::pagination.previous-button` - Button to go to the previous page, used on mobile and by "simple pagination"
- `tables::pagination.next-button` - Button to go to the next page, used on mobile and by "simple pagination"
- `tables::pagination.item-button` - Button to go to a specific page or the next / previous page, used by length-aware pagination on desktop
- `tables::reorder.handle` - Handle to grab in order to reorder a record with drag and drop
- `tables::search-input` - Search input

### Notifications icon aliases

- `notifications::database.modal.empty-state` - Empty state of the database notifications modal
- `notifications::notification` - Aside the textual content of a notification
- `notifications::notification.close-button` - Button to close a notification

### Infolists icon aliases

- `infolists::components.hint` - Aside an entry's hint
- `infolists::components.icon-entry` - Icon entry
- `infolists::components.text-entry` - Aside a text entry's textual content

### Widgets icon aliases

- `widgets::stats-overview.card` - Aside a stats overview card's label
- `widgets::stats-overview.card.description` - Aside a stats overview card's description

### Core components icon aliases

- `support::button` - Button
- `support::dropdown.header` - Dropdown header
- `support::dropdown.list.item` - Dropdown item in a list
- `support::icon-button` - Icon button
- `support::link` - Link
- `support::modal` - Inside a modal's content
- `support::modal.close-button` - Button to close a modal
- `support::section.collapse-button` - Button to collapse a section
- `support::section.icon` - Aside a section's heading
- `support::tabs.item` - Tab item
