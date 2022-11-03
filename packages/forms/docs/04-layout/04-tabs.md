---
title: Tabs
---

Some forms can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Label 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 3')
            ->schema([
                // ...
            ]),
    ])
```

## Setting the default active tab

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Label 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 3')
            ->schema([
                // ...
            ]),
    ])
    ->activeTab(2)
```

## Setting a tab icon or badge

Tabs may have an icon and badge, which you can set using the `icon()` and `badge()` methods:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell') // [tl! focus:start]
            ->badge('39') // [tl! focus:end]
            ->schema([
                // ...
            ]),
        // ...
    ])
```
