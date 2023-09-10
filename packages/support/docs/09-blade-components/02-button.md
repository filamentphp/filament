---
title: Button Blade component
---

## Overview

The button component is used to render a clickable button that can perform an action:

```blade
<x-filament::button wire:click="openNewUserModal">
    New user
</x-filament::button>
```

## Using a button as an anchor link

By default, a button's underlying HTML tag is `<button>`. You can change it to be an `<a>` tag by using the `tag` attribute:

```blade
<x-filament::button
    href="https://filamentphp.com"
    tag="a"
>
    Filament
</x-filament::button>
```

## Setting the size of a button

By default, the size of a button is "medium". You can make it "extra small", "small", "large" or "extra large" by using the `size` attribute:

```blade
<x-filament::button size="xs">
    New user
</x-filament::button>

<x-filament::button size="sm">
    New user
</x-filament::button>

<x-filament::button size="lg">
    New user
</x-filament::button>

<x-filament::button size="xl">
    New user
</x-filament::button>
```

## Changing the color of a button

By default, the color of a button is "primary". You can change it to be `danger`, `gray`, `info`, `success` or `warning` by using the `color` attribute:

```blade
<x-filament::button color="danger">
    New user
</x-filament::button>

<x-filament::button color="gray">
    New user
</x-filament::button>

<x-filament::button color="info">
    New user
</x-filament::button>

<x-filament::button color="success">
    New user
</x-filament::button>

<x-filament::button color="warning">
    New user
</x-filament::button>
```

## Adding an icon to a button

You can add an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to a button by using the `icon` attribute:

```blade
<x-filament::button icon="heroicon-m-sparkles">
    New user
</x-filament::button>
```

You can also change the icon's position to be after the text instead of before it, using the `icon-position` attribute:

```blade
<x-filament::button
    icon="heroicon-m-sparkles"
    icon-position="after"
>
    New user
</x-filament::button>
```

## Making a button outlined

You can make a button use an "outlined" design using the `outlined` attribute:

```blade
<x-filament::button outlined>
    New user
</x-filament::button>
```

## Adding a tooltip to a button

You can add a tooltip to a button by using the `tooltip` attribute:

```blade
<x-filament::button tooltip="Register a user">
    New user
</x-filament::button>
```

## Adding a badge to a button

You can render a [badge](badge) on top of a button by using the `badge` slot:

```blade
<x-filament::button>
    Mark notifications as read
    
    <x-slot name="badge">
        3
    </x-slot>
</x-filament::button>
```

You can [change the color](badge#changing-the-color-of-the-badge) of the badge using the `badge-color` attribute:

```blade
<x-filament::button badge-color="danger">
    Mark notifications as read
    
    <x-slot name="badge">
        3
    </x-slot>
</x-filament::button>
```
