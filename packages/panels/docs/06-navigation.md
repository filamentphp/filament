---
title: Navigation
---

## Overview

By default, Filament will register navigation items for each of your [resources](resources) and [custom pages](pages). These classes contain static properties and methods that you can override, to configure that navigation item.

## Customizing a navigation item's label

By default, the navigation label is generated from the resource or page's name. You may customize this using the `$navigationLabel` property:

```php
protected static ?string $navigationLabel = 'Custom Navigation Label';
```

Alternatively, you may override the `getNavigationLabel()` method:

```php
public static function getNavigationLabel(): ?string
{
    return 'Custom Navigation Label';
}
```

## Customizing a navigation item's icon

To customize a navigation item's [icon](https://blade-ui-kit.com/blade-icons?set=1#search), you may override the `$navigationIcon` property on the [resource](resources) or [page](pages) class:

```php
protected static ?string $navigationIcon = 'heroicon-o-document-text';
```

### Switching navigation item icon when it is active

You may assign a navigation [icon](https://blade-ui-kit.com/blade-icons?set=1#search) which will only be used for active items using the `$activeNavigationIcon` property:

```php
protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
```

## Sorting navigation items

By default, navigation items are sorted alphabetically. You may customize this using the `$navigationSort` property:

```php
protected static ?int $navigationSort = 3;
```

Now, navigation items with a lower sort value will appear before those with a higher sort value - the order is ascending.

## Adding a badge to a navigation item

To add a badge next to the navigation item, you can use the `getNavigationBadge()` method and return the content of the badge:

```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
```

If a badge value is returned by `getNavigationBadge()`, it will display using the primary color by default. To style the badge contextually, return either `danger`, `gray`, `info`, `primary`, `secondary`, `success` or `warning` from the `getNavigationBadgeColor()` method:

```php
public static function getNavigationBadgeColor(): ?string
{
    return static::getModel()::count() > 10 ? 'warning' : 'primary';
}
```

## Grouping navigation items

You may group navigation items by specifying a `$navigationGroup` property on a [resource](resources) and [custom page](pages):

```php
protected static ?string $navigationGroup = 'Settings';
```

All items in the same navigation group will be displayed together under the same group label, "Settings" in this case. Ungrouped items will remain at the top of the sidebar.

### Customizing navigation groups

You may customize navigation groups by calling `navigationGroups()` in the [configuration](configuration), and passing `NavigationGroup` objects in order:

```php
use Filament\Navigation\NavigationGroup;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigationGroups([
            NavigationGroup::make()
                 ->label('Shop')
                 ->icon('heroicon-s-shopping-cart'),
            NavigationGroup::make()
                ->label('Blog')
                ->icon('heroicon-s-pencil'),
            NavigationGroup::make()
                ->label('Settings')
                ->icon('heroicon-s-cog-6-tooth')
                ->collapsed(),
        ]);
}
```

In this example, we pass in a custom `icon()` for the groups, and make one `collapsed()` by default.

#### Ordering navigation groups

By using `navigationGroups()`, you are defining a new order for the navigation groups in the sidebar. If you just want to reorder the groups and not define an entire `NavigationGroup` object, you may just pass the labels of the groups in the new order:

```php
$panel
    ->navigationGroups([
        'Shop',
        'Blog',
        'Settings',
    ])
```

### Making navigation groups not collapsible

By default, navigation groups are collapsible. You may disable this behavior by calling `collapsible(false)` on the `NavigationGroup` object:

```php
use Filament\Navigation\NavigationGroup;

NavigationGroup::make()
    ->label('Settings')
    ->icon('heroicon-s-cog-6-tooth')
    ->collapsible(false);
```

Or, you can do it globally for all groups in the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->collapsibleNavigationGroups(false);
}
```

## Collapsible sidebar on desktop

To make the sidebar collapsible on desktop as well as mobile, you can use the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarCollapsibleOnDesktop();
}
```

By default, when you collapse the sidebar on desktop, the navigation icons still show. You can fully collapse the sidebar using the `sidebarFullyCollapsibleOnDesktop()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarFullyCollapsibleOnDesktop();
}
```

## Registering custom navigation items

To register new navigation items, you can use the [configuration](configuration):

```php
use Filament\Navigation\NavigationItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigationItems([
            NavigationItem::make('Analytics')
                ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                ->icon('heroicon-o-presentation-chart-line')
                ->activeIcon('heroicon-s-presentation-chart-line')
                ->group('Reports')
                ->sort(3),
            // ...
        ]);
}
```

## Conditionally hiding navigation items

You can also conditionally hide a navigation item by using the `visible()` or `hidden()` methods, passing in a condition to check:

```php
use Filament\Navigation\NavigationItem;

NavigationItem::make('Analytics')
    ->visible(auth()->user()->can('view-analytics'))
    // or
    ->hidden(! auth()->user()->can('view-analytics')),
```

## Disabling resource or page navigation items

To prevent resources or pages from showing up in navigation, you may use:

```php
protected static bool $shouldRegisterNavigation = false;
```

## Advanced navigation customization

The `navigation()` method can be called from the [configuration](configuration). It allows you to build a custom navigation that overrides Filament's automatically generated items. This API is designed to give you complete control over the navigation.

### Registering custom navigation items

To register navigation items, call the `items()` method:

```php
use App\Filament\Pages\Settings;
use App\Filament\Resources\UserResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            return $builder->items([
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->activeIcon('heroicon-s-home')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.dashboard'))
                    ->url(route('filament.pages.dashboard')),
                ...UserResource::getNavigationItems(),
                ...Settings::getNavigationItems(),
            ]);
        });
}
```

### Registering custom navigation groups

If you want to register groups, you can call the `groups()` method:

```php
use App\Filament\Pages\HomePageSettings;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\PageResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            return $builder->groups([
                NavigationGroup::make('Website')
                    ->items([
                        ...PageResource::getNavigationItems(),
                        ...CategoryResource::getNavigationItems(),
                        ...HomePageSettings::getNavigationItems(),
                    ]),
            ]);
        });
}
```

### Disabling navigation

You may disable navigation entirely by passing `false` to the `navigation()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigation(false);
}
```

## Customizing the user menu

The user menu is featured in the top right corner of the admin layout. It's fully customizable.

To register new items to the user menu, you can use the [configuration](configuration):

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            MenuItem::make()
                ->label('Settings')
                ->url(route('filament.pages.settings'))
                ->icon('heroicon-m-cog-6-tooth'),
            // ...
        ]);
}
```

### Customizing the account link

To customize the user account link at the start of the user menu, register a new item with the `account` array key:

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            'account' => MenuItem::make()->url(route('filament.pages.account')),
            // ...
        ]);
}
```

### Customizing the logout link

To customize the user account link at the end of the user menu, register a new item with the `logout` array key:

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            'logout' => MenuItem::make()->label('Log out'),
            // ...
        ]);
}
```

## Disabling breadcrumbs

The default layout will show breadcrumbs to indicate the location of the current page within the hierarchy of the app.

You may disable breadcrumbs in your [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->breadcrumbs(false);
}
```
