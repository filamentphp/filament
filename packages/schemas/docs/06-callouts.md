---
title: Callouts
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Callouts are used to draw attention to important information or messages. They are often used for alerts, notices, or tips. You can create a callout using the `Callout` component:

```php
use Filament\Schemas\Components\Callout;

Callout::make('New version available')
    ->description('Filament v4 has been released with exciting new features and improvements.')
    ->info()
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing static values, the `make()` and `description()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/simple" alt="Callout" version="4.x" />

## Using status variants

Callouts have built-in status variants that automatically set the appropriate icon, icon color, and background color. You can use the `danger()`, `info()`, `success()`, or `warning()` methods:

```php
use Filament\Schemas\Components\Callout;

Callout::make('Payment successful')
    ->description('Your order has been confirmed and is being processed.')
    ->success()

Callout::make('Session expiring soon')
    ->description('Your session will expire in 5 minutes. Save your work to avoid losing changes.')
    ->warning()

Callout::make('Connection failed')
    ->description('Unable to connect to the server. Please check your internet connection.')
    ->danger()
```

<AutoScreenshot name="schemas/layout/callout/statuses" alt="Callout statuses" version="4.x" />

## Removing the background color

By default, status callouts have a colored background. You can remove the background color while keeping the status icon and icon color by using `color(null)`:

```php
use Filament\Schemas\Components\Callout;

Callout::make('Scheduled maintenance')
    ->description('The system will be unavailable on Sunday from 2:00 AM to 4:00 AM.')
    ->warning()
    ->color(null)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `color()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/without-background" alt="Callout without background" version="4.x" />

## Adding a custom icon

You can add a custom [icon](../styling/icons) to the callout using the `icon()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;

Callout::make('Pro tip')
    ->description('You can use keyboard shortcuts to navigate faster. Press ? to see all available shortcuts.')
    ->icon(Heroicon::OutlinedLightBulb)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `icon()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Changing the icon color

You can change the icon color using the `iconColor()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;

Callout::make('Pro tip')
    ->description('You can use keyboard shortcuts to navigate faster. Press ? to see all available shortcuts.')
    ->icon(Heroicon::OutlinedLightBulb)
    ->iconColor('primary')
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `iconColor()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/custom-icon" alt="Callout with custom icon" version="4.x" />

### Changing the icon size

By default, the icon size is "large". You can change it to "small" or "medium" using the `iconSize()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Enums\IconSize;

Callout::make('Quick note')
    ->description('This callout has a smaller icon.')
    ->info()
    ->iconSize(IconSize::Small)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `iconSize()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Using a custom background color

You can set a custom background color using the `color()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;

Callout::make('Pro tip')
    ->description('You can use keyboard shortcuts to navigate faster. Press ? to see all available shortcuts.')
    ->color('primary')
    ->icon(Heroicon::OutlinedLightBulb)
    ->iconColor('primary')
```

<AutoScreenshot name="schemas/layout/callout/custom-color" alt="Callout with custom color" version="4.x" />

## Adding actions to the callout footer

You can add [actions](../actions) to the callout footer using the `actions()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Callout;

Callout::make('Your trial ends in 3 days')
    ->description('Upgrade now to keep access to all premium features.')
    ->warning()
    ->actions([
        Action::make('upgrade')
            ->label('Upgrade to Pro')
            ->button(),
        Action::make('compare')
            ->label('Compare plans'),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `actions()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/actions" alt="Callout with actions" version="4.x" />

### Changing the footer actions alignment

By default, actions are aligned to the start. You can change the alignment using the `footerActionsAlignment()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Callout;
use Filament\Support\Enums\Alignment;

Callout::make('Updates available')
    ->description('New features and improvements are ready to install.')
    ->info()
    ->actions([
        Action::make('install')->label('Install Now'),
        Action::make('later')->label('Remind Me Later'),
    ])
    ->footerActionsAlignment(Alignment::End)
```

The available alignment options are `Alignment::Start`, `Alignment::Center`, `Alignment::End`, and `Alignment::Between`.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `footerActionsAlignment()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/actions-aligned-end" alt="Callout with actions aligned to the end" version="4.x" />

## Adding custom footer content

You can add custom content to the footer using the `footer()` method. This accepts an array of schema components:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Text;

Callout::make('Backup complete')
    ->description('Your data has been successfully backed up to the cloud.')
    ->success()
    ->footer([
        Text::make('Last backup: 5 minutes ago')
            ->color('gray'),
        Action::make('viewBackups')
            ->label('View All Backups')
            ->button(),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `footer()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/footer" alt="Callout with custom footer content" version="4.x" />
