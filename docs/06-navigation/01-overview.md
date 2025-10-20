---
title: Overview
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

By default, Filament will register navigation items for each of your [resources](../resources), [custom pages](custom-pages), and [clusters](clusters). These classes contain static properties and methods that you can override, to configure that navigation item.

If you're looking to add a second layer of navigation to your app, you can use [clusters](clusters). These are useful for grouping resources and pages together.

## Customizing a navigation item's label

By default, the navigation label is generated from the resource or page's name. You may customize this using the `$navigationLabel` property:

```php
protected static ?string $navigationLabel = 'Custom Navigation Label';
```

Alternatively, you may override the `getNavigationLabel()` method:

```php
public static function getNavigationLabel(): string
{
    return 'Custom Navigation Label';
}
```

## Customizing a navigation item's icon

To customize a navigation item's [icon](../styling/icons), you may override the `$navigationIcon` property on the [resource](resources) or [page](pages) class:

```php
use BackedEnum;

protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
```

<AutoScreenshot name="panels/navigation/change-icon" alt="Changed navigation item icon" version="3.x" />

If you set `$navigationIcon = null` on all items within the same navigation group, those items will be joined with a vertical bar below the group label.

### Switching navigation item icon when it is active

You may assign a navigation [icon](../styling/icons) which will only be used for active items using the `$activeNavigationIcon` property:

```php
use BackedEnum;

protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-o-document-text';
```

<AutoScreenshot name="panels/navigation/active-icon" alt="Different navigation item icon when active" version="3.x" />

## Sorting navigation items

By default, navigation items are sorted alphabetically. You may customize this using the `$navigationSort` property:

```php
protected static ?int $navigationSort = 3;
```

Now, navigation items with a lower sort value will appear before those with a higher sort value - the order is ascending.

<AutoScreenshot name="panels/navigation/sort-items" alt="Sort navigation items" version="3.x" />

## Adding a badge to a navigation item

To add a badge next to the navigation item, you can use the `getNavigationBadge()` method and return the content of the badge:

```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
```

<AutoScreenshot name="panels/navigation/badge" alt="Navigation item with badge" version="3.x" />

If a badge value is returned by `getNavigationBadge()`, it will display using the primary color by default. To style the badge contextually, return either `danger`, `gray`, `info`, `primary`, `success` or `warning` from the `getNavigationBadgeColor()` method:

```php
public static function getNavigationBadgeColor(): ?string
{
    return static::getModel()::count() > 10 ? 'warning' : 'primary';
}
```

<AutoScreenshot name="panels/navigation/badge-color" alt="Navigation item with badge color" version="3.x" />

A custom tooltip for the navigation badge can be set in `$navigationBadgeTooltip`:

```php
protected static ?string $navigationBadgeTooltip = 'The number of users';
```

Or it can be returned from `getNavigationBadgeTooltip()`:

```php
public static function getNavigationBadgeTooltip(): ?string
{
    return 'The number of users';
}
```

<AutoScreenshot name="panels/navigation/badge-tooltip" alt="Navigation item with badge tooltip" version="3.x" />

## Grouping navigation items

You may group navigation items by specifying a `$navigationGroup` property on a [resource](resources) and [custom page](custom-pages):

```php
use UnitEnum;

protected static string | UnitEnum | null $navigationGroup = 'Settings';
```

<AutoScreenshot name="panels/navigation/group" alt="Grouped navigation items" version="3.x" />

All items in the same navigation group will be displayed together under the same group label, "Settings" in this case. Ungrouped items will remain at the start of the navigation.

### Grouping navigation items under other items

You may group navigation items as children of other items, by passing the label of the parent item as the `$navigationParentItem`:

```php
use UnitEnum;

protected static ?string $navigationParentItem = 'Notifications';

protected static string | UnitEnum | null $navigationGroup = 'Settings';
```

You may also use the `getNavigationParentItem()` method to set a dynamic parent item label:

```php
public static function getNavigationParentItem(): ?string
{
    return __('filament/navigation.groups.settings.items.notifications');
}
```

As seen above, if the parent item has a navigation group, that navigation group must also be defined, so the correct parent item can be identified.

<Aside variant="tip">
    If you're reaching for a third level of navigation like this, you should consider using [clusters](clusters) instead, which are a logical grouping of resources and custom pages, which can share their own separate navigation.
</Aside>

### Customizing navigation groups

You may customize navigation groups by calling `navigationGroups()` in the [configuration](../panel-configuration), and passing `NavigationGroup` objects in order:

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
                 ->icon('heroicon-o-shopping-cart'),
            NavigationGroup::make()
                ->label('Blog')
                ->icon('heroicon-o-pencil'),
            NavigationGroup::make()
                ->label(fn (): string => __('navigation.settings'))
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsed(),
        ]);
}
```

In this example, we pass in a custom `icon()` for the groups, and make one `collapsed()` by default.

#### Ordering navigation groups

By using `navigationGroups()`, you are defining a new order for the navigation groups. If you just want to reorder the groups and not define an entire `NavigationGroup` object, you may just pass the labels of the groups in the new order:

```php
$panel
    ->navigationGroups([
        'Shop',
        'Blog',
        'Settings',
    ])
```

#### Making navigation groups not collapsible

By default, navigation groups are collapsible.

<AutoScreenshot name="panels/navigation/group-collapsible" alt="Collapsible navigation groups" version="3.x" />

You may disable this behavior by calling `collapsible(false)` on the `NavigationGroup` object:

```php
use Filament\Navigation\NavigationGroup;

NavigationGroup::make()
    ->label('Settings')
    ->icon('heroicon-o-cog-6-tooth')
    ->collapsible(false);
```

<AutoScreenshot name="panels/navigation/group-not-collapsible" alt="Not collapsible navigation groups" version="3.x" />

Or, you can do it globally for all groups in the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->collapsibleNavigationGroups(false);
}
```

#### Adding extra HTML attributes to navigation groups

You can pass extra HTML attributes to the navigation group, which will be merged onto the outer DOM element. Pass an array of attributes to the `extraSidebarAttributes()` or `extraTopbarAttributes()` method, where the key is the attribute name and the value is the attribute value:

```php
NavigationGroup::make()
    ->extraSidebarAttributes(['class' => 'featured-sidebar-group']),
    ->extraTopbarAttributes(['class' => 'featured-topbar-group']),
```

The `extraSidebarAttributes()` will be applied to navigation group elements contained in the sidebar, and the `extraTopbarAttributes()` will only be applied to topbar navigation group dropdowns when using [top navigation](#using-top-navigation).

### Registering navigation groups with an enum

You can use an enum class to register navigation groups, which allows you to control their labels, icons, and order in a single place, without needing to register them in the [configuration](../panel-configuration).

To do this, you can create an enum class with cases for each group:

```php
enum NavigationGroup
{
    case Shop;
    
    case Blog;
    
    case Settings;
}
```

To order that the cases are defined in will control the order of the navigation groups.

To use an enum navigation group for a resource or custom page, you can set the `$navigationGroup` property to the enum case:

```php
protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Shop;
```

You can also implement the `HasLabel` interface on the enum class, to define a custom label for each group:

```php
use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Shop;
    
    case Blog;
    
    case Settings;

    public function getLabel(): string
    {
        return match ($this) {
            self::Shop => __('navigation-groups.shop'),
            self::Blog => __('navigation-groups.blog'),
            self::Settings => __('navigation-groups.settings'),
        };
    }
}
```

You can also implement the `HasIcon` interface on the enum class, to define a custom icon for each group:

```php
use Filament\Support\Contracts\HasIcon;

enum NavigationGroup implements HasIcon
{
    case Shop;
    
    case Blog;
    
    case Settings;

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Shop => 'heroicon-o-shopping-cart',
            self::Blog => 'heroicon-o-pencil',
            self::Settings => 'heroicon-o-cog-6-tooth',
        };
    }
}
```

## Collapsible sidebar on desktop

To make the sidebar collapsible on desktop as well as mobile, you can use the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarCollapsibleOnDesktop();
}
```

<AutoScreenshot name="panels/navigation/sidebar-collapsible-on-desktop" alt="Collapsible sidebar on desktop" version="3.x" />

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

<AutoScreenshot name="panels/navigation/sidebar-fully-collapsible-on-desktop" alt="Fully collapsible sidebar on desktop" version="3.x" />

### Navigation groups in a collapsible sidebar on desktop

<Aside variant="info">
    This section only applies to `sidebarCollapsibleOnDesktop()`, not `sidebarFullyCollapsibleOnDesktop()`, since the fully collapsible UI just hides the entire sidebar instead of changing its design.
</Aside>

When using a collapsible sidebar on desktop, you will also often be using [navigation groups](#grouping-navigation-items). By default, the labels of each navigation group will be hidden when the sidebar is collapsed, since there is no space to display them. Even if the navigation group itself is [collapsible](#making-navigation-groups-not-collapsible), all items will still be visible in the collapsed sidebar, since there is no group label to click on to expand the group.

These issues can be solved, to achieve a very minimal sidebar design, by [passing an `icon()`](#customizing-navigation-groups) to the navigation group objects. When an icon is defined, the icon will be displayed in the collapsed sidebar instead of the items at all times. When the icon is clicked, a dropdown will open to the side of the icon, revealing the items in the group.

When passing an icon to a navigation group, even if the items also have icons, the expanded sidebar UI will not show the item icons. This is to keep the navigation hierarchy clear, and the design minimal. However, the icons for the items will be shown in the collapsed sidebar's dropdowns though, since the hierarchy is already clear from the fact that the dropdown is open.

## Registering custom navigation items

To register new navigation items, you can use the [configuration](../panel-configuration):

```php
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use function Filament\Support\original_request;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigationItems([
            NavigationItem::make('Analytics')
                ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                ->icon('heroicon-o-presentation-chart-line')
                ->group('Reports')
                ->sort(3),
            NavigationItem::make('dashboard')
                ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                ->url(fn (): string => Dashboard::getUrl())
                ->isActiveWhen(fn () => original_request()->routeIs('filament.admin.pages.dashboard')),
            // ...
        ]);
}
```

## Conditionally hiding navigation items

You can also conditionally hide a navigation item by using the `visible()` or `hidden()` methods, passing in a condition to check:

```php
use Filament\Navigation\NavigationItem;

NavigationItem::make('Analytics')
    ->visible(fn(): bool => auth()->user()->can('view-analytics'))
    // or
    ->hidden(fn(): bool => ! auth()->user()->can('view-analytics')),
```

## Disabling resource or page navigation items

To prevent resources or pages from showing up in navigation, you may use:

```php
protected static bool $shouldRegisterNavigation = false;
```

Or, you may override the `shouldRegisterNavigation()` method:

```php
public static function shouldRegisterNavigation(): bool
{
    return false;
}
```

Please note that these methods do not control direct access to the resource or page. They only control whether the resource or page will show up in the navigation. If you want to also control access, then you should use [resource authorization](../resources#authorization) or [page authorization](custom-pages#authorization).

## Using top navigation

By default, Filament will use a sidebar navigation. You may use a top navigation instead by using the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->topNavigation();
}
```

<AutoScreenshot name="panels/navigation/top-navigation" alt="Top navigation" version="3.x" />

## Customizing the width of the sidebar

You can customize the width of the sidebar by passing it to the `sidebarWidth()` method in the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarWidth('40rem');
}
```

Additionally, if you are using the `sidebarCollapsibleOnDesktop()` method, you can customize width of the collapsed icons by using the `collapsedSidebarWidth()` method in the [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarCollapsibleOnDesktop()
        ->collapsedSidebarWidth('9rem');
}
```

## Advanced navigation customization

The `navigation()` method can be called from the [configuration](../panel-configuration). It allows you to build a custom navigation that overrides Filament's automatically generated items. This API is designed to give you complete control over the navigation.

### Registering custom navigation items

To register navigation items, call the `items()` method:

```php
use App\Filament\Pages\Settings;
use App\Filament\Resources\Users\UserResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use function Filament\Support\original_request;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            return $builder->items([
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn (): bool => original_request()->routeIs('filament.admin.pages.dashboard'))
                    ->url(fn (): string => Dashboard::getUrl()),
                ...UserResource::getNavigationItems(),
                ...Settings::getNavigationItems(),
            ]);
        });
}
```

<AutoScreenshot name="panels/navigation/custom-items" alt="Custom navigation items" version="3.x" />

### Registering custom navigation groups

If you want to register groups, you can call the `groups()` method:

```php
use App\Filament\Pages\HomePageSettings;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Pages\PageResource;
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

<AutoScreenshot name="panels/navigation/disabled-navigation" alt="Disabled navigation sidebar" version="3.x" />

### Disabling the topbar

You may disable topbar entirely by passing `false` to the `topbar()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->topbar(false);
}
```

### Replacing the sidebar and topbar Livewire components

You may completely replace the Livewire components that are used to render the sidebar and topbar, passing your own Livewire component class name into the `sidebarLivewireComponent()` or `topbarLivewireComponent()` method:

```php
use App\Livewire\Sidebar;
use App\Livewire\Topbar;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->sidebarLivewireComponent(Sidebar::class)
        ->topbarLivewireComponent(Topbar::class);
}
```

## Disabling breadcrumbs

The default layout will show breadcrumbs to indicate the location of the current page within the hierarchy of the app.

You may disable breadcrumbs in your [configuration](../panel-configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->breadcrumbs(false);
}
```

## Reloading the sidebar and topbar

Once a page in the panel is loaded, the sidebar and topbar are not reloaded until you navigate away from the page, or until a menu item is clicked to trigger an action. You can manually reload these components to update them by dispatching a `refresh-sidebar` or `refresh-topbar` browser event.

To dispatch an event from PHP, you can call the `$this->dispatch()` method from any Livewire component, such as a page class, relation manager class, or widget class:

```php
$this->dispatch('refresh-sidebar');
```

When your code does not live inside a Livewire component, such as when you have a custom action class, you can inject the `$livewire` argument into a closure function, and call `dispatch()` on that:

```php
use Filament\Actions\Action;
use Livewire\Component;

Action::make('create')
    ->action(function (Component $livewire) {
        // ...
    
        $livewire->dispatch('refresh-sidebar');
    })
```

Alternatively, you can dispatch an event from JavaScript using the `$dispatch()` Alpine.js helper method, or the native browser `window.dispatchEvent()` method:

```html
<button x-on:click="$dispatch('refresh-sidebar')" type="button">
    Refresh Sidebar
</button>
```

```javascript
window.dispatchEvent(new CustomEvent('refresh-sidebar'));
```
