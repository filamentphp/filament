---
title: Tabs
---

## Overview

Some infolists can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
```

## Setting the default active tab

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->activeTab(2)
```

## Persisting the current tab in the URL's query string

By default, the current tab is not persisted in the URL's query string. You can change this behavior using the `persistTabInQueryString()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString()
```

By default, the current tab is persisted in the URL's query string using the `tab` key. You can change this key by passing it to the `persistTabInQueryString()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString('settings-tab')
```

## Setting a tab icon

Tabs may have an [icon](https://blade-ui-kit.com/blade-icons?set=1#search), which you can set using the `icon()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

### Setting the tab icon color

The icon of the tab may have a color to indicate its significance using the `iconColor()` method. It may be either `primary`, `gray`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->iconColor('primary')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

### Setting the tab icon position

The icon of the tab may be positioned before or after the label using the `iconPosition()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->iconPosition('after')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

## Setting a tab badge

Tabs may have a badge, which you can set using the `badge()` method:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->badge(5)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

## Using grid columns within a tab

You may use the `columns()` method to customize the [grid](grid) within the tab:

```php
use Filament\Infolists\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ])
            ->columns(3),
        // ...
    ])
```
