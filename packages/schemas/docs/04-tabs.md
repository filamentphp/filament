---
title: Tabs
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Some schemas can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `make()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/simple" alt="Tabs" version="4.x" />

## Setting the default active tab

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->activeTab(2)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `activeTab()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Setting a tab icon

Tabs may have an [icon](../styling/icons), which you can set using the `icon()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Notifications')
            ->icon(Heroicon::Bell)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `icon()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/icons" alt="Tabs with icons" version="4.x" />

### Setting the tab icon position

The icon of the tab may be positioned before or after the label using the `iconPosition()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Notifications')
            ->icon(Heroicon::Bell)
            ->iconPosition(IconPosition::After)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `iconPosition()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/icons-after" alt="Tabs with icons after their labels" version="4.x" />

## Setting a tab badge

Tabs may have a badge, which you can set using the `badge()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Notifications')
            ->badge(5)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `badge()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/badges" alt="Tabs with badges" version="4.x" />

If you'd like to change the [color](../styling/colors) for a badge, you can use the `badgeColor()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Notifications')
            ->badge(5)
            ->badgeColor('info')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `badgeColor()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/badges-color" alt="Tabs with badges with color" version="4.x" />

## Using grid columns within a tab

You may use the `columns()` method to customize the [grid](layouts#grid-system) within the tab:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ])
            ->columns(3),
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `columns()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Disabling scrollable tabs

Tabs are rendered horizontally by default, and are scrollable when they exceed the available width.

You may disable scrolling using the `scrollable(false)` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        // ...
    ])
    ->scrollable(false)
```

When tabs are not scrollable, the component automatically detects the available width. If not all tabs can fit, a dropdown button will appear. Any tabs that exceed the available width will be grouped inside this dropdown automatically.

<AutoScreenshot name="schemas/layout/tabs/not-scrollable" alt="Non-scrollable tabs with overflow dropdown" version="4.x" />

## Using vertical tabs

You can render the tabs vertically by using the `vertical()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->vertical()
```

<AutoScreenshot name="schemas/layout/tabs/vertical" alt="Vertical tabs" version="4.x" />

Optionally, you can pass a boolean value to the `vertical()` method to control if the tabs should be rendered vertically or not:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        // ...
    ])
    ->vertical(FeatureFlag::active())
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `vertical()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Removing the styled container

By default, tabs and their content are wrapped in a container styled as a card. You may remove the styled container using `contained()`:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->contained(false)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `contained()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Persisting the current tab in the user's session

By default, the current tab is not persisted in the browser's local storage. You can change this behavior using the `persistTab()` method. You must also pass in a unique `id()` for the tabs component, to distinguish it from all other sets of tabs in the app. This ID will be used as the key in the local storage to store the current tab:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        // ...
    ])
    ->persistTab()
    ->id('order-tabs')
```

Optionally, the `persistTab()` method accepts a boolean value to control if the active tab should persist or not:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        // ...
    ])
    ->persistTab(FeatureFlag::active())
    ->id('order-tabs')
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing static values, the `persistTab()` and `id()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

### Persisting the current tab in the URL's query string

By default, the current tab is not persisted in the URL's query string. You can change this behavior using the `persistTabInQueryString()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString()
```

When enabled, the current tab is persisted in the URL's query string using the `tab` key. You can change this key by passing it to the `persistTabInQueryString()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->tabs([
        Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString('settings-tab')
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `persistTabInQueryString()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
