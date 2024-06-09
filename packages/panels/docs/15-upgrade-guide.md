---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/panels/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v10.0+
- Livewire v3.0+

Please upgrade Filament before upgrading to Livewire v3. Instructions on how to upgrade Livewire can be found [here](https://livewire.laravel.com/docs/upgrading).

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles most breaking changes.

```bash
composer require filament/upgrade:"^3.2" -W --dev

vendor/bin/filament-v3
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

A new `app/Providers/Filament/*PanelProvider.php` file will be created, and the configuration from your old `config/filament.php` file should be copied. Since this is a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get an error while trying to access your panel, then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

Finally, you must run `php artisan filament:install` to finalize the Filament v3 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it anymore.

> Some plugins you're using may not be available in v3 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v3-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

After upgrading the dependency via Composer, you should execute `php artisan filament:upgrade` in order to clear any Laravel caches and publish the new frontend assets.

### High-impact changes

#### Panel provider instead of the config file

The Filament v2 config file grew pretty big, and now it is incredibly small. Most of the configuration is now done in a service provider, which provides a cleaner API, more type safety, IDE autocomplete support, and [the ability to create multiple panels in your app](configuration#introducing-panels). We call these special configuration service providers **"panel providers"**.

Before you can create the new panel provider, make sure that you've got Filament v3 installed with Composer. Then, run the following command:

```bash
php artisan filament:install --panels
```

A new `app/Providers/Filament/AdminPanelProvider.php` file will be created, ready for you to transfer over your old configuration from the `config/filament.php` file. Since this is a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get an error while trying to access your panel, then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

Most configuration transfer is very self-explanatory, but if you get stuck, please refer to the [configuration documentation](configuration).

This will especially affect configuration done via the `Filament::serving()` method, which was used for theme customization, navigation and menu registration. Consult the [configuration](configuration), [navigation](navigation) and [themes](themes) documentation sections.

Finally, you can run the following command to replace the old config file with the shiny new one:

```bash
php artisan vendor:publish --tag=filament-config --force
```

#### `FILAMENT_FILESYSTEM_DRIVER` .env variable

The `FILAMENT_FILESYSTEM_DRIVER` .env variable has been renamed to `FILAMENT_FILESYSTEM_DISK`. This is to make it more consistent with Laravel, as Laravel v9 introduced this change as well. Please ensure that you update your .env files accordingly, and don't forget production!

#### Resource and relation manager imports

Some classes that are imported in resources and relation managers have moved:

- `Filament\Resources\Form` has moved to `Filament\Forms\Form`
- `Filament\Resources\Table` has moved to `Filament\Tables\Table`

#### Method signature changes

User model (with `FilamentUser` interface):

- `canAccessFilament()` has been renamed to `canAccessPanel()` and has a new `\Filament\Panel $panel` parameter

Resource classes:

- `applyGlobalSearchAttributeConstraint()` now has a `string $search` parameter before `$searchAttributes()` instead of `$searchQuery` after
- `getGlobalSearchEloquentQuery()` is public
- `getGlobalSearchResults()`has a `$search` parameter instead of `$searchQuery`
- `getRouteBaseName()` has a new `?string $panel` parameter

Resource classes and all page classes, including resource pages, custom pages, settings pages, and dashboard pages:

- `getActiveNavigationIcon()` is public
- `getNavigationBadge()` is public
- `getNavigationBadgeColor()` is public
- `getNavigationGroup()` is public
- `getNavigationIcon()` is public
- `getNavigationLabel()` is public
- `getNavigationSort()` is public
- `getNavigationUrl()` is public
- `shouldRegisterNavigation()` is public

All page classes, including resource pages, custom pages, settings pages, and custom dashboard pages:

- `getBreadcrumbs()` is public
- `getFooterWidgetsColumns()` is public
- `getHeader()` is public
- `getHeaderWidgetsColumns()` is public
- `getHeading()` is public
- `getRouteName()` has a new `?string $panel` parameter
- `getSubheading()` is public
- `getTitle()` is public
- `getVisibleFooterWidgets()` is public
- `getVisibleHeaderWidgets()` is public

List and Manage resource pages:

- `table()` is public

Create resource pages:

- `canCreateAnother()` is public

Edit and View resource pages:

- `getFormTabLabel()` is now `getContentTabLabel()`

Relation managers:

- `form()` is no longer static
- `getInverseRelationshipName()` return type is now `?string`
- `table()` is no longer static

Custom dashboard pages:

- `getDashboard()` is public
- `getWidgets()` is public

#### Property signature changes

Resource classes and all page classes, including resource pages, custom pages, settings pages, and dashboard pages:

- `$middlewares` is now `$routeMiddleware`

#### Heroicons have been updated to v2

The Heroicons library has been updated to v2. This means that any icons you use in your app may have changed names. You can find a list of changes [here](https://github.com/tailwindlabs/heroicons/releases/tag/v2.0.0).

### Medium-impact changes

#### Date-time pickers

The date-time picker form field now uses the browser's native date picker by default. It usually has a better UX than the old date picker, but you may notice features missing, bad browser compatibility, or behavioral bugs. If you want to revert to the old date picker, you can use the `native(false)` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')
    ->native(false)
```

#### Secondary color

Filament v2 had a `secondary` color for many components which was gray. All references to `secondary` should be replaced with `gray` to preserve the same appearance. This frees `secondary` to be registered to a new custom color of your choice.

#### `$get` and `$set` closure parameters

In the Form Builder package, `$get` and `$set` parameters now use a type of either `\Filament\Forms\Get` or `\Filament\Forms\Set` instead of `\Closure`. This allows for better IDE autocomplete support of each function's parameters.

An easy way to upgrade your code quickly is to find and replace:

- `Closure $get` to `\Filament\Forms\Get $get`
- `Closure $set` to `\Filament\Forms\Set $set`

#### Blade icon components have been disabled

During v2, we noticed performance issues with Blade icon components. We've decided to disable them by default in v3, so we only use the [`@svg()` syntax](https://github.com/blade-ui-kit/blade-icons#directive) for rendering icons.

A side effect of this change is that all custom icons that you use must now be [registered in a set](https://github.com/blade-ui-kit/blade-icons#defining-sets). We no longer allow arbitrary Blade components to be used as custom icons.

#### Logo customization

In v2, you can customize the logo of the admin panel using a `/resources/views/vendor/filament/components/brand.blade.php` file. In v3, this has been moved to the new `brandLogo()` API. You can now [set the brand logo](themes#adding-a-logo) by adding it to your panel configuration.

#### Plugins

Filament v3 has a new universal plugin system that breaches the constraints of the admin panel. Learn how to build v3 plugins [here](plugins).

### Low-impact changes

#### Default actions and type-specific relation manager classes

> If you started the Filament project after v2.13, you can skip this section. Since then, new resources and relation managers have been generated with the new syntax.

Since v2.13, resources and relation managers now define actions within the `table()` method instead of them being assumed by default.

When using simple resources, remove the `CanCreateRecords`, `CanDeleteRecords`, `CanEditRecords`, and `CanViewRecords` traits from the Manage page.

We also deprecated type-specific relation manager classes. Any classes extending `BelongsToManyRelationManager`, `HasManyRelationManager`, `HasManyThroughRelationManager`, `MorphManyRelationManager`, or `MorphToManyRelationManager` should now extend `\Filament\Resources\RelationManagers\RelationManager`. You can also remove the `CanAssociateRecords`, `CanAttachRecords`, `CanCreateRecords`, `CanDeleteRecords`, `CanDetachRecords`, `CanDisassociateRecords`, `CanEditRecords`, and `CanViewRecords` traits from relation managers.

To learn more about v2.13 changes, read our [blog post](https://filamentphp.com/blog/v2130-admin-resources).

#### Blade components

Some Blade components have been moved to different namespaces:

- `<x-filament::page>` is now `<x-filament-panels::page>`
- `<x-filament::widget>` is now `<x-filament-widgets::widget>`

However, aliases have been set up so that you don't need to change your code.

#### Resource pages without a `$resource` property

Filament v2 allowed for resource pages to be created without a `$resource` property. In v3 you must declare this, else you may end up with the error:

`Typed static property Filament\Resources\Pages\Page::$resource must not be accessed before initialization`

You should ensure that the `$resource` property is set on all resource pages:

```php
protected static string $resource = PostResource::class;
```
