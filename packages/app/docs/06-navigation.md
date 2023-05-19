---
title: Navigation
---

## Overview

By default, Filament will register navigation items for each of your [resources](resources) and [custom pages](pages). These classes contain static properties and methods that you can override, to configure that navigation item and its order:

```php
protected static ?string $navigationIcon = 'heroicon-o-document-text';

protected static ?string $navigationLabel = 'Custom Navigation Label';

protected static ?int $navigationSort = 3;
```

The `$navigationIcon` supports the name of any Blade component, and passes a set of formatting classes to it. By default, the [Blade Heroicons v1](https://github.com/blade-ui-kit/blade-heroicons/tree/1.3.1) package is installed, so you may use the name of any [Heroicons v1](https://v1.heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

## Navigation item badges

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
use Filament\Context;
use Filament\Navigation\NavigationGroup;

public function context(Context $context): Context
{
    return $context
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
$context
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
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->collapsibleNavigationGroups(false);
}
```

## Collapsible sidebar on desktop

To make the sidebar collapsible on desktop as well as mobile, you can use the [configuration](configuration):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->sidebarCollapsibleOnDesktop();
}
```

By default, when you collapse the sidebar on desktop, the navigation icons still show. You can fully collapse the sidebar using the `sidebarFullyCollapsibleOnDesktop()` method:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->sidebarFullyCollapsibleOnDesktop();
}
```

## Active icons

You may assign a navigation icon which will be displayed for active items using the `$activeNavigationIcon` property:

```php
protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
```

Alternatively, override the `getActiveNavigationIcon()` method:

```php
public static function getActiveNavigationIcon(): string
{
    return 'heroicon-s-document-text';
}
```

## Registering custom navigation items

To register new navigation items, you can use the [configuration](configuration):

```php
use Filament\Context;
use Filament\Navigation\NavigationItem;

public function context(Context $context): Context
{
    return $context
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
use Filament\Context;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;

public function context(Context $context): Context
{
    return $context
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
use Filament\Context;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;

public function context(Context $context): Context
{
    return $context
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
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->navigation(false);
}
```

## Customizing the user menu

The user menu is featured in the top right corner of the admin layout. It's fully customizable.

To register new items to the user menu, you can use the [configuration](configuration):

```php
use Filament\Context;
use Filament\Navigation\MenuItem;

public function context(Context $context): Context
{
    return $context
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
use Filament\Context;
use Filament\Navigation\MenuItem;

public function context(Context $context): Context
{
    return $context
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
use Filament\Context;
use Filament\Navigation\MenuItem;

public function context(Context $context): Context
{
    return $context
        // ...
        ->userMenuItems([
            'logout' => MenuItem::make()->label('Log out'),
            // ...
        ]);
}
```

## Breadcrumbs

The default layout will show breadcrumbs to indicate the location of the current page within the hierarchy of the app.

You may disable breadcrumbs in your [configuration](configuration):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->breadcrumbs(false);
}
```
