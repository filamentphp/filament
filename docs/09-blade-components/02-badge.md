---
title: Badge Blade component
---

## Overview

The badge component is used to render a small box with some text inside:

```blade
<x-filament::badge>
    New
</x-filament::badge>
```

## Setting the size of a badge

By default, the size of a badge is "medium". You can make it "extra small" or "small" by using the `size` attribute:

```blade
<x-filament::badge size="xs">
    New
</x-filament::badge>

<x-filament::badge size="sm">
    New
</x-filament::badge>
```

## Changing the color of the badge

By default, the color of a badge is "primary". You can change it to be `danger`, `gray`, `info`, `success` or `warning` by using the `color` attribute:

```blade
<x-filament::badge color="danger">
    New
</x-filament::badge>

<x-filament::badge color="gray">
    New
</x-filament::badge>

<x-filament::badge color="info">
    New
</x-filament::badge>

<x-filament::badge color="success">
    New
</x-filament::badge>

<x-filament::badge color="warning">
    New
</x-filament::badge>
```

## Adding an icon to a badge

You can add an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to a badge by using the `icon` attribute:

```blade
<x-filament::badge icon="heroicon-m-sparkles">
    New
</x-filament::badge>
```

You can also change the icon's position to be after the text instead of before it, using the `icon-position` attribute:

```blade
<x-filament::badge
    icon="heroicon-m-sparkles"
    icon-position="after"
>
    New
</x-filament::badge>
```
