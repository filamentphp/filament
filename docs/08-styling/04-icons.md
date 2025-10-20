---
title: Icons
---

## Introduction

Icons are used throughout the entire Filament UI to visually communicate core parts of the user experience. To render icons, we use the [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package from Blade UI Kit.

They have a website where you can [search all the available icons](https://blade-ui-kit.com/blade-icons?set=1#search) from various Blade Icons packages. Each package contains a different icon set that you can choose from. Filament installs the "Heroicons" icon set by default, so if you are using icons from this set you do not need to install any additional packages.

## Using Heroicons in Filament

Filament includes the [Heroicons](https://heroicons.com) icon set by default. You can use any of the icons from this set in your Filament application without installing any additional packages. The `Heroicon` enum class allows you to leverage your IDE's autocompletion features to find the icon you want to use:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

Action::make('star')
    ->icon(Heroicon::OutlinedStar)
    
Toggle::make('is_starred')
    ->onIcon(Heroicon::Star)
```

Each icon comes with an "outlined" and "solid" variant, with the "outlined" variant's name being prefixed with `Outlined`. For example, the `Heroicon::Star` icon is the solid variant, while the `Heroicon::OutlinedStar` icon is the outlined variant.

The Heroicons set includes multiple sizes (16px, 20px and 24px) of solid icon, and when using the `Heroicon` enum class, Filament will automatically use the correct size for the context in which you are using it.

If you would like to use an icon in a [Blade component](../components), you can pass it as an attribute:

```blade
@php
    use Filament\Support\Icons\Heroicon;
@endphp

<x-filament::badge :icon="Heroicon::Star">
    Star
</x-filament::badge>
```

## Using other icon sets in Filament

Once you have [found an icon](https://blade-ui-kit.com/blade-icons?set=1#search), installed the icon set (if it's not a Heroicon) you would like to use in Filament, you need to use its name. For example, if you wanted to use the [`iconic-star`](https://blade-ui-kit.com/blade-icons/iconic-star) icon, you could pass it to an icon method of a PHP component like so:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;

Action::make('star')
    ->icon('iconic-star')
    
Toggle::make('is_starred')
    ->onIcon('iconic-check-circle')
```

If you would like to use an icon in a [Blade component](../components), you can pass it as an attribute:

```blade
<x-filament::badge icon="iconic-star">
    Star
</x-filament::badge>
```

## Using custom SVGs as icons

The [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package allows you to register custom SVGs as icons. This is useful if you want to use your own custom icons in Filament.

To start with, publish the Blade Icons configuration file:

```bash
php artisan vendor:publish --tag=blade-icons
```

Now, open the `config/blade-icons.php` file, and uncomment the `default` set in the `sets` array.

Now that the default set exists in the config file, you can simply put any icons you want inside the `resources/svg` directory of your application. For example, if you put an SVG file named `star.svg` inside the `resources/svg` directory, you can reference it anywhere in Filament as `icon-star` (see below). The `icon-` prefix is configurable in the `config/blade-icons.php` file too. You can also render the custom icon in a Blade view using the [`@svg('icon-star')` directive](https://github.com/blade-ui-kit/blade-icons#directive).

```php
use Filament\Actions\Action;

Action::make('star')
    ->icon('icon-star')
```

## Replacing the default icons

Filament includes an icon management system that allows you to replace any icons that are used by default in the UI with your own. This happens in the `boot()` method of any service provider, like `AppServiceProvider`, or even a dedicated service provider for icons. If you wanted to build a plugin to replace Heroicons with a different set, you could absolutely do that by creating a Laravel package with a similar service provider.

To replace an icon, you can use the `FilamentIcon` facade. It has a `register()` method, which accepts an array of icons to replace. The key of the array is the unique [icon alias](#available-icon-aliases) that identifies the icon in the Filament UI, and the value is name of a Blade icon to replace it instead. Alternatively, you may use HTML instead of an icon name to render an icon from a Blade view for example:

```php
use Filament\Support\Facades\FilamentIcon;
use Filament\View\PanelsIconAlias;

FilamentIcon::register([
    PanelsIconAlias::GLOBAL_SEARCH_FIELD => 'fas-magnifying-glass',
    PanelsIconAlias::SIDEBAR_GROUP_COLLAPSE_BUTTON => view('icons.chevron-up'),
]);
```

## Available icon aliases

### Actions icon aliases

Using class `Filament\Actions\View\ActionsIconAlias`

- `ActionsIconAlias::ACTION_GROUP` - Trigger button of an action group
- `ActionsIconAlias::CREATE_ACTION_GROUPED` - Trigger button of a grouped create action
- `ActionsIconAlias::DELETE_ACTION` - Trigger button of a delete action
- `ActionsIconAlias::DELETE_ACTION_GROUPED` - Trigger button of a grouped delete action
- `ActionsIconAlias::DELETE_ACTION_MODAL` - Modal of a delete action
- `ActionsIconAlias::DETACH_ACTION` - Trigger button of a detach action
- `ActionsIconAlias::DETACH_ACTION_MODAL` - Modal of a detach action
- `ActionsIconAlias::DISSOCIATE_ACTION` - Trigger button of a dissociate action
- `ActionsIconAlias::DISSOCIATE_ACTION_MODAL` - Modal of a dissociate action
- `ActionsIconAlias::EDIT_ACTION` - Trigger button of an edit action
- `ActionsIconAlias::EDIT_ACTION_GROUPED` - Trigger button of a grouped edit action
- `ActionsIconAlias::EXPORT_ACTION_GROUPED` - Trigger button of a grouped export action
- `ActionsIconAlias::FORCE_DELETE_ACTION` - Trigger button of a force-delete action
- `ActionsIconAlias::FORCE_DELETE_ACTION_GROUPED` - Trigger button of a grouped force-delete action
- `ActionsIconAlias::FORCE_DELETE_ACTION_MODAL` - Modal of a force-delete action
- `ActionsIconAlias::IMPORT_ACTION_GROUPED` - Trigger button of a grouped import action
- `ActionsIconAlias::MODAL_CONFIRMATION` - Modal of an action that requires confirmation
- `ActionsIconAlias::REPLICATE_ACTION` - Trigger button of a replicate action
- `ActionsIconAlias::REPLICATE_ACTION_GROUPED` - Trigger button of a grouped replicate action
- `ActionsIconAlias::RESTORE_ACTION` - Trigger button of a restore action
- `ActionsIconAlias::RESTORE_ACTION_GROUPED` - Trigger button of a grouped restore action
- `ActionsIconAlias::RESTORE_ACTION_MODAL` - Modal of a restore action
- `ActionsIconAlias::VIEW_ACTION` - Trigger button of a view action
- `ActionsIconAlias::VIEW_ACTION_GROUPED` - Trigger button of a grouped view action

### Forms icon aliases

Using class `Filament\Forms\View\FormsIconAlias`

- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_CLONE` - Trigger button of a clone action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_COLLAPSE` - Trigger button of a collapse action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_DELETE` - Trigger button of a delete action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_EXPAND` - Trigger button of an expand action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_DOWN` - Trigger button of a move down action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_UP` - Trigger button of a move up action in a builder item
- `FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_REORDER` - Trigger button of a reorder action in a builder item
- `FormsIconAlias::COMPONENTS_CHECKBOX_LIST_SEARCH_FIELD` - Search input in a checkbox list
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_DRAG_CROP` - Trigger button of a drag crop action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_DRAG_MOVE` - Trigger button of a drag move action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_FLIP_HORIZONTAL` - Trigger button of a flip horizontal action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_FLIP_VERTICAL` - Trigger button of a flip vertical action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_DOWN` - Trigger button of a move down action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_LEFT` - Trigger button of a move left action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_RIGHT` - Trigger button of a move right action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_UP` - Trigger button of a move up action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ROTATE_LEFT` - Trigger button of a rotate left action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ROTATE_RIGHT` - Trigger button of a rotate right action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_100` - Trigger button of a zoom 100 action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_IN` - Trigger button of a zoom in action in a file upload editor
- `FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_OUT` - Trigger button of a zoom out action in a file upload editor
- `FormsIconAlias::COMPONENTS_KEY_VALUE_ACTIONS_DELETE` - Trigger button of a delete action in a key-value field item
- `FormsIconAlias::COMPONENTS_KEY_VALUE_ACTIONS_REORDER` - Trigger button of a reorder action in a key-value field item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_CLONE` - Trigger button of a clone action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_COLLAPSE` - Trigger button of a collapse action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_DELETE` - Trigger button of a delete action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_EXPAND` - Trigger button of an expand action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_DOWN` - Trigger button of a move down action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_UP` - Trigger button of a move up action in a repeater item
- `FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_REORDER` - Trigger button of a reorder action in a repeater item
- `FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCKS_CLOSE_BUTTON` - Close button for custom blocks panel in a rich editor
- `FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_DELETE_BUTTON` - Delete button for a custom block in a rich editor
- `FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_EDIT_BUTTON` - Edit button for a custom block in a rich editor
- `FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_MERGE_TAGS_CLOSE_BUTTON` - Close button for merge tags panel in a rich editor
- `FormsIconAlias::COMPONENTS_SELECT_ACTIONS_CREATE_OPTION` - Trigger button of a create option action in a select field
- `FormsIconAlias::COMPONENTS_SELECT_ACTIONS_EDIT_OPTION` - Trigger button of an edit option action in a select field
- `FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_HIDE_PASSWORD` - Trigger button of a hide password action in a text input field
- `FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_SHOW_PASSWORD` - Trigger button of a show password action in a text input field
- `FormsIconAlias::COMPONENTS_TOGGLE_BUTTONS_BOOLEAN_FALSE` - "False" option of a `boolean()` toggle buttons field
- `FormsIconAlias::COMPONENTS_TOGGLE_BUTTONS_BOOLEAN_TRUE` - "True" option of a `boolean()` toggle buttons field

### Infolists icon aliases

Using class `Filament\Infolists\View\InfolistsIconAlias`

- `InfolistsIconAlias::COMPONENTS_ICON_ENTRY_FALSE` - Falsy state of an icon entry
- `InfolistsIconAlias::COMPONENTS_ICON_ENTRY_TRUE` - Truthy state of an icon entry

### Notifications icon aliases

Using class `Filament\Notifications\View\NotificationsIconAlias`

- `NotificationsIconAlias::DATABASE_MODAL_EMPTY_STATE` - Empty state of the database notifications modal
- `NotificationsIconAlias::NOTIFICATION_CLOSE_BUTTON` - Button to close a notification
- `NotificationsIconAlias::NOTIFICATION_DANGER` - Danger notification
- `NotificationsIconAlias::NOTIFICATION_INFO` - Info notification
- `NotificationsIconAlias::NOTIFICATION_SUCCESS` - Success notification
- `NotificationsIconAlias::NOTIFICATION_WARNING` - Warning notification

### Panels icon aliases

Using class `Filament\View\PanelsIconAlias`

- `PanelsIconAlias::GLOBAL_SEARCH_FIELD` - Global search field
- `PanelsIconAlias::PAGES_DASHBOARD_ACTIONS_FILTER` - Trigger button of the dashboard filter action
- `PanelsIconAlias::PAGES_DASHBOARD_NAVIGATION_ITEM` - Dashboard page navigation item
- `PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN` - Trigger button of the login action on the request password reset page
- `PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN_RTL` - Trigger button of the login action on the request password reset page (right-to-left direction)
- `PanelsIconAlias::RESOURCES_PAGES_EDIT_RECORD_NAVIGATION_ITEM` - Resource edit record page navigation item
- `PanelsIconAlias::RESOURCES_PAGES_MANAGE_RELATED_RECORDS_NAVIGATION_ITEM` - Resource manage related records page navigation item
- `PanelsIconAlias::RESOURCES_PAGES_VIEW_RECORD_NAVIGATION_ITEM` - Resource view record page navigation item
- `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON` - Button to collapse the sidebar
- `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL` - Button to collapse the sidebar (right-to-left direction)
- `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON` - Button to expand the sidebar
- `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL` - Button to expand the sidebar (right-to-left direction)
- `PanelsIconAlias::SIDEBAR_GROUP_COLLAPSE_BUTTON` - Collapse button for a sidebar group
- `PanelsIconAlias::SIDEBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON` - Button to open the database notifications modal
- `PanelsIconAlias::TENANT_MENU_BILLING_BUTTON` - Billing button in the tenant menu
- `PanelsIconAlias::TENANT_MENU_PROFILE_BUTTON` - Profile button in the tenant menu
- `PanelsIconAlias::TENANT_MENU_REGISTRATION_BUTTON` - Registration button in the tenant menu
- `PanelsIconAlias::TENANT_MENU_TOGGLE_BUTTON` - Button to toggle the tenant menu
- `PanelsIconAlias::THEME_SWITCHER_LIGHT_BUTTON` - Button to switch to the light theme from the theme switcher
- `PanelsIconAlias::THEME_SWITCHER_DARK_BUTTON` - Button to switch to the dark theme from the theme switcher
- `PanelsIconAlias::THEME_SWITCHER_SYSTEM_BUTTON` - Button to switch to the system theme from the theme switcher
- `PanelsIconAlias::TOPBAR_CLOSE_SIDEBAR_BUTTON` - Button to close the sidebar
- `PanelsIconAlias::TOPBAR_OPEN_SIDEBAR_BUTTON` - Button to open the sidebar
- `PanelsIconAlias::TOPBAR_GROUP_TOGGLE_BUTTON` - Toggle button for a topbar group
- `PanelsIconAlias::TOPBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON` - Button to open the database notifications modal
- `PanelsIconAlias::USER_MENU_PROFILE_ITEM` - Profile item in the user menu
- `PanelsIconAlias::USER_MENU_LOGOUT_BUTTON` - Button in the user menu to log out
- `PanelsIconAlias::USER_MENU_TOGGLE_BUTTON` - Button to toggle the user menu 
- `PanelsIconAlias::WIDGETS_ACCOUNT_LOGOUT_BUTTON` - Button in the account widget to log out
- `PanelsIconAlias::WIDGETS_FILAMENT_INFO_OPEN_DOCUMENTATION_BUTTON` - Button to open the documentation from the Filament info widget
- `PanelsIconAlias::WIDGETS_FILAMENT_INFO_OPEN_GITHUB_BUTTON` - Button to open GitHub from the Filament info widget

### Schema icon aliases

Using class `Filament\Schemas\View\SchemaIconAlias`

- `SchemaIconAlias::COMPONENTS_WIZARD_COMPLETED_STEP` - Completed step in a wizard

### Tables icon aliases

Using class `Filament\Tables\View\TablesIconAlias`

- `TablesIconAlias::ACTIONS_DISABLE_REORDERING` - Trigger button of the disable reordering action
- `TablesIconAlias::ACTIONS_ENABLE_REORDERING` - Trigger button of the enable reordering action
- `TablesIconAlias::ACTIONS_FILTER` - Trigger button of the filter action
- `TablesIconAlias::ACTIONS_GROUP` - Trigger button of a group records action
- `TablesIconAlias::ACTIONS_OPEN_BULK_ACTIONS` - Trigger button of an open bulk actions action
- `TablesIconAlias::ACTIONS_COLUMN_MANAGER` - Trigger button of the column manager action
- `TablesIconAlias::COLUMNS_COLLAPSE_BUTTON` - Button to collapse a column
- `TablesIconAlias::COLUMNS_ICON_COLUMN_FALSE` - Falsy state of an icon column
- `TablesIconAlias::COLUMNS_ICON_COLUMN_TRUE` - Truthy state of an icon column
- `TablesIconAlias::EMPTY_STATE` - Empty state icon
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_BOOLEAN` - Default icon for a boolean constraint in the query builder
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_DATE` - Default icon for a date constraint in the query builder
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_NUMBER` - Default icon for a number constraint in the query builder
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_RELATIONSHIP` - Default icon for a relationship constraint in the query builder
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_SELECT` - Default icon for a select constraint in the query builder
- `TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT` - Default icon for a text constraint in the query builder
- `TablesIconAlias::FILTERS_REMOVE_ALL_BUTTON` - Button to remove all filters
- `TablesIconAlias::GROUPING_COLLAPSE_BUTTON` - Button to collapse a group of records
- `TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON` - Sort button of a column sorted in ascending order
- `TablesIconAlias::HEADER_CELL_SORT_BUTTON` - Sort button of a column when it is currently not sorted
- `TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON` - Sort button of a column sorted in descending order
- `TablesIconAlias::REORDER_HANDLE` - Handle to grab in order to reorder a record with drag and drop
- `TablesIconAlias::SEARCH_FIELD` - Search input

### UI components icon aliases

Using class `Filament\Support\View\SupportIconAlias`

- `SupportIconAlias::BADGE_DELETE_BUTTON` - Button to delete a badge
- `SupportIconAlias::BREADCRUMBS_SEPARATOR` - Separator between breadcrumbs
- `SupportIconAlias::BREADCRUMBS_SEPARATOR_RTL` - Separator between breadcrumbs (right-to-left direction)
- `SupportIconAlias::MODAL_CLOSE_BUTTON` - Button to close a modal
- `SupportIconAlias::PAGINATION_FIRST_BUTTON` - Button to go to the first page
- `SupportIconAlias::PAGINATION_FIRST_BUTTON_RTL` - Button to go to the first page (right-to-left direction)
- `SupportIconAlias::PAGINATION_LAST_BUTTON` - Button to go to the last page
- `SupportIconAlias::PAGINATION_LAST_BUTTON_RTL` - Button to go to the last page (right-to-left direction)
- `SupportIconAlias::PAGINATION_NEXT_BUTTON` - Button to go to the next page
- `SupportIconAlias::PAGINATION_NEXT_BUTTON_RTL` - Button to go to the next page (right-to-left direction)
- `SupportIconAlias::PAGINATION_PREVIOUS_BUTTON` - Button to go to the previous page
- `SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL` - Button to go to the previous page (right-to-left direction)
- `SupportIconAlias::SECTION_COLLAPSE_BUTTON` - Button to collapse a section

### Widgets icon aliases

Using class `Filament\Widgets\View\WidgetsIconAlias`

- `WidgetsIconAlias::CHART_WIDGET_FILTER` - Button of the filter action
