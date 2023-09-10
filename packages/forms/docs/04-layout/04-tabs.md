---
title: Tabs
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Some forms can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
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

<AutoScreenshot name="forms/layout/tabs/simple" alt="Tabs" version="3.x" />

## Setting the default active tab

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
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
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
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
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
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
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="forms/layout/tabs/icons" alt="Tabs with icons" version="3.x" />

### Setting the tab icon position

The icon of the tab may be positioned before or after the label using the `iconPosition()` method:

```php
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\IconPosition;

Tabs::make('Label')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->iconPosition(IconPosition::After)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="forms/layout/tabs/icons-after" alt="Tabs with icons after their labels" version="3.x" />

## Setting a tab badge

Tabs may have a badge, which you can set using the `badge()` method:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->badge(5)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="forms/layout/tabs/badges" alt="Tabs with badges" version="3.x" />

## Using grid columns within a tab

You may use the `columns()` method to customize the [grid](grid) within the tab:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
    ->tabs([
        Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ])
            ->columns(3),
        // ...
    ])
```

## Removing the styled container

By default, tabs and their content are wrapped in a container styled as a card. You may remove the styled container using `contained()`:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Label')
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
    ->contained(false)
```

