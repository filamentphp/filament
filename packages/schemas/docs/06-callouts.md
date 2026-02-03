---
title: Callouts
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Callouts are used to draw attention to important information or messages. They are often used for alerts, notices, or tips. You can create a callout using the `Callout` component:

```php
use Filament\Schemas\Components\Callout;

Callout::make('Important Notice')
    ->description('Please read this information carefully before proceeding.')
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing static values, the `make()` and `description()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/simple" alt="Callout" version="4.x" />

## Using status variants

Callouts have built-in status variants that automatically set the appropriate icon, icon color, and background color. You can use the `danger()`, `info()`, `success()`, or `warning()` methods:

```php
use Filament\Schemas\Components\Callout;

Callout::make('Error')
    ->description('Something went wrong. Please try again.')
    ->danger()

Callout::make('Information')
    ->description('Here is some helpful information.')
    ->info()

Callout::make('Success')
    ->description('Your changes have been saved.')
    ->success()

Callout::make('Warning')
    ->description('Please review the following items.')
    ->warning()
```

<AutoScreenshot name="schemas/layout/callout/statuses" alt="Callout statuses" version="4.x" />

## Removing the background color

By default, status callouts have a colored background. You can remove the background color while keeping the status icon and icon color by using `color(null)`:

```php
use Filament\Schemas\Components\Callout;

Callout::make('Important Notice')
    ->description('This callout has no background color.')
    ->danger()
    ->color(null)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `color()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/callout/without-background" alt="Callout without background" version="4.x" />

## Adding a custom icon

You can add a custom [icon](../styling/icons) to the callout using the `icon()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;

Callout::make('Tip')
    ->description('You can use custom icons for your callouts.')
    ->icon(Heroicon::Sparkles)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `icon()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Changing the icon color

You can change the icon color using the `iconColor()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;

Callout::make('Custom Icon')
    ->description('The icon color is independent of the background color.')
    ->icon(Heroicon::ShieldCheck)
    ->iconColor('success')
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `iconColor()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Changing the icon size

By default, the icon size is "large". You can change it to "small" or "medium" using the `iconSize()` method:

```php
use Filament\Schemas\Components\Callout;
use Filament\Support\Enums\IconSize;

Callout::make('Small Icon')
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

Callout::make('Announcement')
    ->description('A special announcement with a custom color.')
    ->color('primary')
    ->icon(Heroicon::Star)
    ->iconColor('primary')
```

<AutoScreenshot name="schemas/layout/callout/custom-color" alt="Callout with custom color" version="4.x" />

## Adding actions to the callout footer

You can add [actions](../actions) to the callout footer using the `actions()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Callout;

Callout::make('Subscription Expiring')
    ->description('Your subscription will expire in 7 days.')
    ->warning()
    ->actions([
        Action::make('renew')
            ->label('Renew Now')
            ->button(),
        Action::make('dismiss')
            ->label('Remind Me Later'),
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

Callout::make('Notice')
    ->description('Actions can be aligned to different positions.')
    ->info()
    ->actions([
        Action::make('action1')->label('Action 1'),
        Action::make('action2')->label('Action 2'),
    ])
    ->footerActionsAlignment(Alignment::End)
```

The available alignment options are `Alignment::Start`, `Alignment::Center`, `Alignment::End`, and `Alignment::Between`.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `footerActionsAlignment()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Adding custom footer content

You can add custom content to the footer using the `footer()` method. This accepts an array of schema components:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Text;

Callout::make('System Status')
    ->description('All systems are operational.')
    ->success()
    ->footer([
        Text::make('Last updated: January 15, 2025')
            ->color('gray'),
        Action::make('refresh')
            ->label('Refresh')
            ->button(),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `footer()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
