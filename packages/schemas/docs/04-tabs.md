---
title: Tabs
---
import Aside from "@components/Aside.astro"
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

## Navigating tabs using the keyboard

You can use Tab to enter the tab headers, then the left and right arrow keys to move focus between available tabs. Home and End focus the first and last available tabs. For vertical tabs, use the up and down arrow keys instead. Moving focus does not change the selected tab; press Enter or Space to select it. Press Tab again to continue to the selected panel.

Hidden tabs and disabled header buttons are skipped. Calling `disabled()` on a `Tab` disables its fields, not its header, so you can still open a read-only tab.

Tab headers are linked to their panels using accessible IDs. If you set custom `id()` values, keep them unique across the page. Use distinct keys for separate tabsets, including nested tabsets with the same tab labels.

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

<UtilityInjection set="schemaComponents" version="4.x" extras="Badge;;?string;;$badge;;The evaluated value of the badge.">As well as allowing a static value, the `badgeColor()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/tabs/badges-color" alt="Tabs with badges with color" version="4.x" />

### Deferring the loading of tab badges

If you have expensive queries powering your tab badges, the initial page load may be slow. You can defer the loading of tab badges using the `deferBadge()` method, which will load the badge values asynchronously after the page has rendered:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Tabs')
    ->key('notifications-tabs')
    ->tabs([
        Tab::make('Notifications')
            ->badge(static fn (): int => Notification::query()->where('unread', true->count())
            ->deferBadge()
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<Aside variant="warning">
    The `badge()` value must be returned from a function when using `deferBadge()`. If you pass a raw value like `badge(Notification::query()->count())`, the query runs immediately when the tab is built, defeating the purpose of deferral.

    The `Tabs` component must have a `key()` set when using `deferBadge()`. Without a key, the deferred badge request cannot identify the correct component on the server.
</Aside>

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `deferBadge()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

While the badges are loading, a small loading indicator will appear in place of each deferred badge. Once the data is fetched, the loading indicators will be replaced with the actual badge values.

## Deferring the loading of tab content

If the content of a tab is expensive to render, you can pass a `Schema` object to its `schema()` method and use `deferLoading()`. The content of the active tab will be loaded when it enters the viewport. The content of an inactive tab will not be loaded until the tab is selected:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

Tabs::make('Settings')
    ->key('settingsTabs')
    ->tabs([
        Tab::make('Profile')
            ->key('profileTab')
            ->schema(
                Schema::make()
                    ->components([
                        TextInput::make('name'),
                    ])
                    ->deferLoading(),
            ),
        Tab::make('Notifications')
            ->key('notificationsTab')
            ->schema(
                Schema::make()
                    ->components([
                        // ...
                    ])
                    ->deferLoading(),
            ),
    ])
```

Every deferred schema must have a unique key. In this example, the keys on the tabs are inherited by their child schemas, while the key on the `Tabs` component namespaces them from other tabs in the same schema. You can learn more in the [schema overview](overview#deferring-the-loading-of-a-child-schema).

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

The dropdown is a visual presentation of the same tab headers, not a separate menu. Arrow keys, Home, and End navigate all available tabs. Focusing an overflowed tab automatically opens the dropdown and displays that tab's focus indicator. Press Enter or Space to select it; the dropdown remains open while you navigate its tabs. Press Tab to leave the headers and close the dropdown. Escape closes it without changing selection and returns focus to an inline tab, or to the selected panel if every tab is overflowed. You can also click the dropdown button to reveal the overflowed tabs.

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

<AutoScreenshot name="schemas/layout/tabs/not-contained" alt="Tabs without a styled container" version="4.x" />

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

## Persisting the current tab in the URL's query string

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

## Using tabs without content panels

If your `livewireProperty()` tabs change a filter instead of showing separate content panels, use `tabPanels(false)`. This renders a labelled group of buttons with a pressed state rather than a tablist. Each button remains reachable using Tab:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make('Status')
    ->livewireProperty('activeStatus')
    ->tabPanels(false)
    ->tabs([
        'all' => Tab::make('All'),
        'published' => Tab::make('Published'),
    ])
```

Resource list filters use this mode automatically. For links that navigate to another page, use the [tabs Blade component](../components/tabs) instead. Navigation links keep their native link behavior and are not schema tab panels.
