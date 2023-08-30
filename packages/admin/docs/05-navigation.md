---
title: Navigation
---

## Getting started

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
protected static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
```

If a badge value is returned by `getNavigationBadge()`, it will display using the primary Tailwind color by default. To style the badge contextually, return either `danger`, `warning`, `success` or `secondary` from the `getNavigationBadgeColor()` method:

```php
protected static function getNavigationBadgeColor(): ?string
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

You may customize navigation groups by calling `Filament::registerNavigationGroups()` from the `boot()` method of any service provider, and passing `NavigationGroup` objects in order:

```php
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                     ->label('Shop')
                     ->icon('heroicon-s-shopping-cart'),
                NavigationGroup::make()
                    ->label('Blog')
                    ->icon('heroicon-s-pencil'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-s-cog')
                    ->collapsed(),
            ]);
        });
    }
}
```

In this example, we pass in a custom `icon()` for the groups, and make one `collapsed()` by default.

#### Ordering navigation groups

By using `registerNavigationGroups()`, you are defining a new order for the navigation groups in the sidebar. If you just want to reorder the groups and not define an entire `NavigationGroup` object, you may just pass the labels of the groups in the new order:

```php
use Filament\Facades\Filament;

Filament::registerNavigationGroups([
    'Shop',
    'Blog',
    'Settings',
]);
```

## Active icons

You may assign a navigation icon which will be displayed for active items using the `$activeNavigationIcon` property:

```php
protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
```

Alternatively, override the `getActiveNavigationIcon()` method:

```php
protected static function getActiveNavigationIcon(): string
{
    return 'heroicon-s-document-text';
}
```

## Registering custom navigation items

You may register custom navigation items by calling `Filament::registerNavigationItems()` from the `boot()` method of any service provider:

```php
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;

Filament::serving(function () {
    Filament::registerNavigationItems([
        NavigationItem::make('Analytics')
            ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
            ->icon('heroicon-o-presentation-chart-line')
            ->activeIcon('heroicon-s-presentation-chart-line')
            ->group('Reports')
            ->sort(3),
    ]);
});
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

The `Filament::navigation()` method which can be called from the `boot` method of a `ServiceProvider`:

```php
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;

Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
    return $builder;
});
```

Once you add this callback function, Filament's default automatic navigation will be disabled and your sidebar will be empty. This is done on purpose, since this API is designed to give you complete control over the navigation.

To register navigation items, just call the `items()` method:

```php
use App\Filament\Pages\Settings;
use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;

Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
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
```

If you want to register groups, you can call the `groups()` method:

```php
use App\Filament\Pages\HomePageSettings;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\PageResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;

Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
    return $builder
        ->groups([
            NavigationGroup::make('Website')
                ->items([
                    ...PageResource::getNavigationItems(),
                    ...CategoryResource::getNavigationItems(),
                    ...HomePageSettings::getNavigationItems(),
                ]),
        ]);
});
```

## Customizing the user menu

The user menu is featured in the top right corner of the admin layout. It's fully customizable.

To register new items to the user menu, you should use a service provider:

```php
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

Filament::serving(function () {
    Filament::registerUserMenuItems([
        UserMenuItem::make()
            ->label('Settings')
            ->url(route('filament.pages.settings'))
            ->icon('heroicon-s-cog'),
        // ...
    ]);
});
```

### Customizing the account link

To customize the user account link at the start of the user menu, register a new item with the `account` array key:

```php
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

Filament::serving(function () {
    Filament::registerUserMenuItems([
        'account' => UserMenuItem::make()->url(route('filament.pages.account')),
        // ...
    ]);
});
```

### Customizing the logout link

To customize the user logout link at the end of the user menu, register a new item with the `logout` array key:

```php
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

Filament::serving(function () {
    Filament::registerUserMenuItems([
        // ...
        'logout' => UserMenuItem::make()->label('Log out'),
    ]);
});
```
