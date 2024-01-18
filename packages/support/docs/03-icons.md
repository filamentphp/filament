---
title: Icons
---

## Overview

Icons are used throughout the entire Filament UI to visually communicate core parts of the user experience. To render icons, we use the [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package from Blade UI Kit.

They have a website where you can [search all the available icons](https://blade-ui-kit.com/blade-icons?set=1#search) from various Blade Icons packages. Each package contains a different icon set that you can choose from.

## Using custom SVGs as icons

The [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package allows you to register custom SVGs as icons. This is useful if you want to use your own custom icons in Filament.

To start with, publish the Blade Icons configuration file:

```bash
php artisan vendor:publish --tag=blade-icons
```

Now, open the `config/blade-icons.php` file, and uncomment the `default` set in the `sets` array.

Now that the default set exists in the config file, you can simply put any icons you want inside the `resources/svg` directory of your application. For example, if you put an SVG file named `star.svg` inside the `resources/svg` directory, you can reference it anywhere in Filament as `icon-star`. The `icon-` prefix is configurable in the `config/blade-icons.php` file too. You can also render the custom icon in a Blade view using the [`@svg('icon-star')` directive](https://github.com/blade-ui-kit/blade-icons#directive).

## Replacing the default icons

Filament includes an icon management system that allows you to replace any icons are used by default in the UI with your own. This happens in the `boot()` method of any service provider, like `AppServiceProvider`, or even a dedicated service provider for icons. If you wanted to build a plugin to replace Heroicons with a different set, you could absolutely do that by creating a Laravel package with a similar service provider.

To replace an icon, you can use the `FilamentIcon` facade. It has a `register()` method, which accepts an array of icons to replace. The key of the array is the unique [icon alias](#available-icon-aliases) that identifies the icon in the Filament UI, and the value is name of a Blade icon to replace it instead. Alternatively, you may use HTML instead of an icon name to render an icon from a Blade view for example:

```php
use Filament\Support\Facades\FilamentIcon;

FilamentIcon::register([
    'panels::topbar.global-search.field' => 'fas-magnifying-glass',
    'panels::sidebar.group.collapse-button' => view('icons.chevron-up'),
]);
```

### Allowing users to customize icons from your plugin

If you have built a Filament plugin, your users may want to be able to customize icons in the same way that they can with any core Filament package. This is possible if you replace any manual `@svg()` usages with the `<x-filament::icon>` Blade component. This component allows you to pass in an icon alias, the name of the SVG icon that should be used by default, and any classes or HTML attributes:

```blade
<x-filament::icon
    alias="panels::topbar.global-search.field"
    icon="heroicon-m-magnifying-glass"
    wire:target="search"
    class="h-5 w-5 text-gray-500 dark:text-gray-400"
/>
```

Alternatively, you may pass an SVG element into the component's slot instead of defining a default icon name:

```blade
<x-filament::icon
    alias="panels::topbar.global-search.field"
    wire:target="search"
    class="h-5 w-5 text-gray-500 dark:text-gray-400"
>
    <svg>
        <!-- ... -->
    </svg>
</x-filament::icon>
```

## Available icon aliases

### Panel Builder icon aliases

- `panels::global-search.field` - Global search field
- `panels::pages.dashboard.actions.filter` - Trigger button of the dashboard filter action
- `panels::pages.dashboard.navigation-item` - Dashboard page navigation item
- `panels::pages.password-reset.request-password-reset.actions.login` - Trigger button of the login action on the request password reset page
- `panels::pages.password-reset.request-password-reset.actions.login.rtl` - Trigger button of the login action on the request password reset page (right-to-left direction)
- `panels::resources.pages.edit-record.navigation-item` - Resource edit record page navigation item
- `panels::resources.pages.manage-related-records.navigation-item` - Resource manage related records page navigation item
- `panels::resources.pages.view-record.navigation-item` - Resource view record page navigation item
- `panels::sidebar.collapse-button` - Button to collapse the sidebar
- `panels::sidebar.collapse-button.rtl` - Button to collapse the sidebar (right-to-left direction)
- `panels::sidebar.expand-button` - Button to expand the sidebar
- `panels::sidebar.expand-button.rtl` - Button to expand the sidebar (right-to-left direction)
- `panels::sidebar.group.collapse-button` - Collapse button for a sidebar group
- `panels::tenant-menu.billing-button` - Billing button in the tenant menu
- `panels::tenant-menu.profile-button` - Profile button in the tenant menu
- `panels::tenant-menu.registration-button` - Registration button in the tenant menu
- `panels::tenant-menu.toggle-button` - Button to toggle the tenant menu
- `panels::theme-switcher.light-button` - Button to switch to the light theme from the theme switcher
- `panels::theme-switcher.dark-button` - Button to switch to the dark theme from the theme switcher
- `panels::theme-switcher.system-button` - Button to switch to the system theme from the theme switcher
- `panels::topbar.close-sidebar-button` - Button to close the sidebar
- `panels::topbar.open-sidebar-button` - Button to open the sidebar
- `panels::topbar.group.toggle-button` - Toggle button for a topbar group
- `panels::topbar.open-database-notifications-button` - Button to open the database notifications modal
- `panels::user-menu.profile-item` - Profile item in the user menu
- `panels::user-menu.logout-button` - Button in the user menu to log out
- `panels::widgets.account.logout-button` - Button in the account widget to log out
- `panels::widgets.filament-info.open-documentation-button` - Button to open the documentation from the Filament info widget
- `panels::widgets.filament-info.open-github-button` - Button to open GitHub from the Filament info widget

### Form Builder icon aliases

- `forms::components.builder.actions.clone` - Trigger button of a clone action in a builder item
- `forms::components.builder.actions.collapse` - Trigger button of a collapse action in a builder item
- `forms::components.builder.actions.delete` - Trigger button of a delete action in a builder item
- `forms::components.builder.actions.expand` - Trigger button of an expand action in a builder item
- `forms::components.builder.actions.move-down` - Trigger button of a move down action in a builder item
- `forms::components.builder.actions.move-up` - Trigger button of a move up action in a builder item
- `forms::components.builder.actions.reorder` - Trigger button of a reorder action in a builder item
- `forms::components.checkbox-list.search-field` - Search input in a checkbox list
- `forms::components.file-upload.editor.actions.drag-crop` - Trigger button of a drag crop action in a file upload editor
- `forms::components.file-upload.editor.actions.drag-move` - Trigger button of a drag move action in a file upload editor
- `forms::components.file-upload.editor.actions.flip-horizontal` - Trigger button of a flip horizontal action in a file upload editor
- `forms::components.file-upload.editor.actions.flip-vertical` - Trigger button of a flip vertical action in a file upload editor
- `forms::components.file-upload.editor.actions.move-down` - Trigger button of a move down action in a file upload editor
- `forms::components.file-upload.editor.actions.move-left` - Trigger button of a move left action in a file upload editor
- `forms::components.file-upload.editor.actions.move-right` - Trigger button of a move right action in a file upload editor
- `forms::components.file-upload.editor.actions.move-up` - Trigger button of a move up action in a file upload editor
- `forms::components.file-upload.editor.actions.rotate-left` - Trigger button of a rotate left action in a file upload editor
- `forms::components.file-upload.editor.actions.rotate-right` - Trigger button of a rotate right action in a file upload editor
- `forms::components.file-upload.editor.actions.zoom-100` - Trigger button of a zoom 100 action in a file upload editor
- `forms::components.file-upload.editor.actions.zoom-in` - Trigger button of a zoom in action in a file upload editor
- `forms::components.file-upload.editor.actions.zoom-out` - Trigger button of a zoom out action in a file upload editor
- `forms::components.key-value.actions.delete` - Trigger button of a delete action in a key-value field item
- `forms::components.key-value.actions.reorder` - Trigger button of a reorder action in a key-value field item
- `forms::components.repeater.actions.clone` - Trigger button of a clone action in a repeater item
- `forms::components.repeater.actions.collapse` - Trigger button of a collapse action in a repeater item
- `forms::components.repeater.actions.delete` - Trigger button of a delete action in a repeater item
- `forms::components.repeater.actions.expand` - Trigger button of an expand action in a repeater item
- `forms::components.repeater.actions.move-down` - Trigger button of a move down action in a repeater item
- `forms::components.repeater.actions.move-up` - Trigger button of a move up action in a repeater item
- `forms::components.repeater.actions.reorder` - Trigger button of a reorder action in a repeater item
- `forms::components.select.actions.create-option` - Trigger button of a create option action in a select field
- `forms::components.select.actions.edit-option` - Trigger button of an edit option action in a select field
- `forms::components.text-input.actions.hide-password` - Trigger button of a hide password action in a text input field
- `forms::components.text-input.actions.show-password` - Trigger button of a show password action in a text input field
- `forms::components.toggle-buttons.boolean.false` - "False" option of a `boolean()` toggle buttons field
- `forms::components.toggle-buttons.boolean.true` - "True" option of a `boolean()` toggle buttons field
- `forms::components.wizard.completed-step` - Completed step in a wizard

### Table Builder icon aliases

- `tables::actions.disable-reordering` - Trigger button of the disable reordering action
- `tables::actions.enable-reordering` - Trigger button of the enable reordering action
- `tables::actions.filter` - Trigger button of the filter action
- `tables::actions.group` - Trigger button of a group records action
- `tables::actions.open-bulk-actions` - Trigger button of an open bulk actions action
- `tables::actions.toggle-columns` - Trigger button of the toggle columns action
- `tables::columns.collapse-button` - Button to collapse a column
- `tables::columns.icon-column.false` - Falsy state of an icon column
- `tables::columns.icon-column.true` - Truthy state of an icon column
- `tables::empty-state` - Empty state icon
- `tables::filters.query-builder.constraints.boolean` - Default icon for a boolean constraint in the query builder
- `tables::filters.query-builder.constraints.date` - Default icon for a date constraint in the query builder
- `tables::filters.query-builder.constraints.number` - Default icon for a number constraint in the query builder
- `tables::filters.query-builder.constraints.relationship` - Default icon for a relationship constraint in the query builder
- `tables::filters.query-builder.constraints.select` - Default icon for a select constraint in the query builder
- `tables::filters.query-builder.constraints.text` - Default icon for a text constraint in the query builder
- `tables::filters.remove-all-button` - Button to remove all filters
- `tables::grouping.collapse-button` - Button to collapse a group of records
- `tables::header-cell.sort-asc-button` - Sort button of a column sorted in ascending order
- `tables::header-cell.sort-desc-button` - Sort button of a column sorted in descending order
- `tables::reorder.handle` - Handle to grab in order to reorder a record with drag and drop
- `tables::search-field` - Search input

### Notifications icon aliases

- `notifications::database.modal.empty-state` - Empty state of the database notifications modal
- `notifications::notification.close-button` - Button to close a notification
- `notifications::notification.danger` - Danger notification
- `notifications::notification.info` - Info notification
- `notifications::notification.success` - Success notification
- `notifications::notification.warning` - Warning notification

### Actions icon aliases

- `actions::action-group` - Trigger button of an action group
- `actions::create-action.grouped` - Trigger button of a grouped create action
- `actions::delete-action` - Trigger button of a delete action
- `actions::delete-action.grouped` - Trigger button of a grouped delete action
- `actions::delete-action.modal` - Modal of a delete action
- `actions::detach-action` - Trigger button of a detach action
- `actions::detach-action.modal` - Modal of a detach action
- `actions::dissociate-action` - Trigger button of a dissociate action
- `actions::dissociate-action.modal` - Modal of a dissociate action
- `actions::edit-action` - Trigger button of an edit action
- `actions::edit-action.grouped` - Trigger button of a grouped edit action
- `actions::export-action.grouped` - Trigger button of a grouped export action
- `actions::force-delete-action` - Trigger button of a force delete action
- `actions::force-delete-action.grouped` - Trigger button of a grouped force delete action
- `actions::force-delete-action.modal` - Modal of a force delete action
- `actions::import-action.grouped` - Trigger button of a grouped import action
- `actions::modal.confirmation` - Modal of an action that requires confirmation
- `actions::replicate-action` - Trigger button of a replicate action
- `actions::replicate-action.grouped` - Trigger button of a grouped replicate action
- `actions::restore-action` - Trigger button of a restore action
- `actions::restore-action.grouped` - Trigger button of a grouped restore action
- `actions::restore-action.modal` - Modal of a restore action
- `actions::view-action` - Trigger button of a view action
- `actions::view-action.grouped` - Trigger button of a grouped view action

### Infolist Builder icon aliases

- `infolists::components.icon-entry.false` - Falsy state of an icon entry
- `infolists::components.icon-entry.true` - Truthy state of an icon entry

### UI components icon aliases

- `badge.delete-button` - Button to delete a badge
- `breadcrumbs.separator` - Separator between breadcrumbs
- `breadcrumbs.separator.rtl` - Separator between breadcrumbs (right-to-left direction)
- `modal.close-button` - Button to close a modal
- `pagination.previous-button` - Button to go to the previous page
- `pagination.previous-button.rtl` - Button to go to the previous page (right-to-left direction)
- `pagination.next-button` - Button to go to the next page
- `pagination.next-button.rtl` - Button to go to the next page (right-to-left direction)
- `section.collapse-button` - Button to collapse a section
