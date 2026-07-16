---
title: Callout Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

A callout can be used to draw attention to important information or messages:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Important Notice
    </x-slot>

    <x-slot name="description">
        Please read this information carefully before proceeding.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/simple" alt="An info callout" version="5.x" />

## Using status colors

You can set the `color` attribute to `danger`, `info`, `success`, or `warning` to create status callouts:

```blade
<x-filament::callout
    icon="heroicon-o-x-circle"
    color="danger"
>
    <x-slot name="heading">
        Error
    </x-slot>

    <x-slot name="description">
        Something went wrong. Please try again.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Information
    </x-slot>

    <x-slot name="description">
        Here is some helpful information.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-check-circle"
    color="success"
>
    <x-slot name="heading">
        Success
    </x-slot>

    <x-slot name="description">
        Your changes have been saved.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-exclamation-circle"
    color="warning"
>
    <x-slot name="heading">
        Warning
    </x-slot>

    <x-slot name="description">
        Please review the following items.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/colors" alt="Callouts in different colors" version="5.x" />

## Adding an icon to the callout

You can add an [icon](../styling/icons) to a callout using the `icon` attribute:

```blade
<x-filament::callout icon="heroicon-o-sparkles">
    <x-slot name="heading">
        Tip
    </x-slot>

    <x-slot name="description">
        You can use custom icons for your callouts.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/custom-icon" alt="A callout with a custom icon" version="5.x" />

### Changing the color of the callout icon

By default, the icon color inherits from the callout's `color`. You can override it using the `icon-color` attribute:

```blade
<x-filament::callout
    icon="heroicon-o-shield-check"
    icon-color="success"
>
    <x-slot name="heading">
        Custom Icon Color
    </x-slot>

    <x-slot name="description">
        The icon color is independent of the background color.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/icon-color" alt="A callout with a custom icon color" version="5.x" />

### Changing the size of the callout icon

By default, the size of the callout icon is "large". You can change it to "small" or "medium" using the `icon-size` attribute:

```blade
<x-filament::callout
    icon="heroicon-m-information-circle"
    icon-size="sm"
    color="info"
>
    <x-slot name="heading">
        Small Icon
    </x-slot>

    <x-slot name="description">
        This callout has a smaller icon.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-m-information-circle"
    icon-size="md"
    color="info"
>
    <x-slot name="heading">
        Medium Icon
    </x-slot>

    <x-slot name="description">
        This callout has a medium icon.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/icon-sizes" alt="Callouts with different icon sizes" version="5.x" />

## Using a custom background color

You can set a custom background color using the `color` attribute with any supported color:

```blade
<x-filament::callout
    icon="heroicon-o-star"
    color="primary"
>
    <x-slot name="heading">
        Announcement
    </x-slot>

    <x-slot name="description">
        A special announcement with a custom color.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/primary-color" alt="A callout with a primary color" version="5.x" />

## Adding content to the footer

You can add custom content to the callout footer using the `footer` slot:

```blade
<x-filament::callout
    icon="heroicon-o-check-circle"
    color="success"
>
    <x-slot name="heading">
        System Status
    </x-slot>

    <x-slot name="description">
        All systems are operational.
    </x-slot>

    <x-slot name="footer">
        <span class="text-sm text-gray-500">Last updated: January 15, 2025</span>
    </x-slot>
</x-filament::callout>
```

You can also include buttons or other interactive elements in the footer:

```blade
<x-filament::callout
    icon="heroicon-o-exclamation-circle"
    color="warning"
>
    <x-slot name="heading">
        Subscription Expiring
    </x-slot>

    <x-slot name="description">
        Your subscription will expire in 7 days.
    </x-slot>

    <x-slot name="footer">
        <x-filament::button size="sm">
            Renew Now
        </x-filament::button>
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/footer" alt="A callout with a footer action" version="5.x" />

## Adding content to the controls

You can add custom content to the callout controls (top-right corner) using the `controls` slot:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Dismissible Callout
    </x-slot>

    <x-slot name="description">
        This callout can be dismissed using the control in the top-right corner.
    </x-slot>

    <x-slot name="controls">
        <x-filament::icon-button
            icon="heroicon-m-x-mark"
            color="gray"
            label="Dismiss"
        />
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/controls" alt="A callout with a dismiss control" version="5.x" />

## Callouts without an icon

Callouts can be rendered without an icon if needed:

```blade
<x-filament::callout>
    <x-slot name="heading">
        No Icon
    </x-slot>

    <x-slot name="description">
        This callout has no icon.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/no-icon" alt="A callout without an icon" version="5.x" />

## Callouts with only a heading

Callouts can be used with just a heading, without a description:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Simple Notice
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/heading-only" alt="A callout with only a heading" version="5.x" />
