---
title: User menu
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The user menu is featured in the top right corner of the admin layout. It's fully customizable.

Each menu item is represented by an [action](../actions), and can be customized in the same way. To register new items, you can pass the actions to the `userMenuItems()` method of the [configuration](../panel-configuration):

```php
use App\Filament\Pages\Settings;
use Filament\Actions\Action;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            Action::make('settings')
                ->url(fn (): string => Settings::getUrl())
                ->icon('heroicon-o-cog-6-tooth'),
            // ...
        ]);
}
```

<AutoScreenshot name="panels/navigation/user-menu" alt="User menu with custom menu item" version="4.x" />

## Grouping user menu items below the theme switcher

By default, every item you register in `userMenuItems()` appears in a **single** list under the theme switcher (except items with a negative [`sort()`](../actions), such as the profile link, which stay above it).

You can split the area **below the theme switcher** into multiple visually separated lists by passing a **list of groups**: each group is a normal `userMenuItems()` array, wrapped as one element of an outer list.

```php
use Filament\Actions\Action;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            [
                Action::make('team')
                    ->url('/team')
                    ->icon('heroicon-o-user-group'),
                Action::make('billing')
                    ->url('/billing')
                    ->icon('heroicon-o-banknotes'),
            ],
            [
                Action::make('localeEn')->label('English')->url('/locale/en'),
                Action::make('localeFr')->label('Français')->url('/locale/fr'),
            ],
        ]);
}
```

Each inner array is rendered as its own [dropdown list](../12-components/03-dropdown) block after the theme switcher. Items with `sort()` less than `0` still follow the usual rules and are not moved into these grouped lists.

### Backward compatibility

- A **single** flat array (the same shape as in the examples above this section) behaves exactly as before: one list under the theme switcher.
- Calling `userMenuItems()` **multiple times** with flat arrays still merges all items into the **last** registered group, so you can split configuration across service providers without changing the visual result.

### Logout action

The default `logout` action is injected if you do not register one. When you use multiple groups, Filament appends `logout` to the **last** after-theme group if it is not already present in any group, so users always retain a way to sign out.

## Moving the user menu to the sidebar

By default, the user menu is positioned in the topbar. If the topbar is disabled, it is added to the sidebar.

You can choose to always move it to the sidebar by passing a `position` argument to the `userMenu()` method in the [configuration](../panel-configuration):

```php
use Filament\Enums\UserMenuPosition;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenu(position: UserMenuPosition::Sidebar);
}
```

<AutoScreenshot name="panels/navigation/user-menu-sidebar" alt="User menu moved to the sidebar" version="4.x" />

## Customizing the profile link

To customize the user profile link at the start of the user menu, register a new item with the `profile` array key, and pass a function that [customizes the action](../actions) object:

```php
use Filament\Actions\Action;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            'profile' => fn (Action $action) => $action->label('Edit profile'),
            // ...
        ]);
}
```

For more information on creating a profile page, check out the [authentication features documentation](../users#authentication-features).

## Customizing the logout link

To customize the user logout link at the end of the user menu, register a new item with the `logout` array key, and pass a function that [customizes the action](../actions) object:

```php
use Filament\Actions\Action;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenuItems([
            'logout' => fn (Action $action) => $action->label('Log out'),
            // ...
        ]);
}
```

## Conditionally hiding user menu items

You can also conditionally hide a user menu item by using the `visible()` or `hidden()` methods, passing in a condition to check. Passing a function will defer condition evaluation until the menu is actually being rendered:

```php
use App\Models\Payment;
use Filament\Actions\Action;

Action::make('payments')
    ->visible(fn (): bool => auth()->user()->can('viewAny', Payment::class))
    // or
    ->hidden(fn (): bool => ! auth()->user()->can('viewAny', Payment::class))
```

## Sending a `POST` HTTP request from a user menu item

You can send a `POST` HTTP request from a user menu item by passing a URL to the `url()` method, and also using `postToUrl()`:

```php
use Filament\Actions\Action;

Action::make('lockSession')
    ->url(fn (): string => route('lock-session'))
    ->postToUrl()
```

## Disabling the user menu

You may disable the user menu entirely by passing `false` to the `userMenu()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userMenu(false);
}
```
