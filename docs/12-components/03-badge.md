---
title: Badge Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The badge component is used to render a small box with some text inside:

```blade
<x-filament::badge>
    New
</x-filament::badge>
```

<AutoScreenshot name="components/badge/simple" alt="A simple badge" version="4.x" />

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

<AutoScreenshot name="components/badge/sizes" alt="Badges in different sizes" version="4.x" />

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

<AutoScreenshot name="components/badge/colors" alt="Badges in different colors" version="4.x" />

## Adding an icon to a badge

You can add an [icon](../styling/icons) to a badge by using the `icon` attribute:

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

<AutoScreenshot name="components/badge/icon" alt="Badges with icons" version="4.x" />

## Using badges in JavaScript renderers

You can import typed Badge components directly into React, Vue 3, or Svelte 5 custom fields, schema components, widgets, and pages. Your application can remain plain JavaScript; Vite compiles the adapters' TypeScript. Install `tippy.js` and `mousetrap` alongside your framework. Strict TypeScript consumers also need `@types/mousetrap`.

```jsx
import Badge from '../../../vendor/filament/support/resources/js/react/Badge'

<Badge color="success">Approved</Badge>
<Badge tag="button" onClick={save} loading={saving}>Save filters</Badge>
<Badge onDelete={remove} deleteLabel="Remove priority filter" deleteLoading={removing}>
    Priority
</Badge>
```

```vue
<script setup>
import Badge from '../../../vendor/filament/support/resources/js/vue/Badge.vue'
</script>

<template>
    <Badge color="success">Approved</Badge>
    <Badge tag="button" @click="save" :loading="saving">Save filters</Badge>
    <Badge :on-delete="remove" delete-label="Remove priority filter" :delete-loading="removing">
        Priority
    </Badge>
</template>
```

```svelte
<script>
    import Badge from '../../../vendor/filament/support/resources/js/svelte/Badge.svelte'
</script>

<Badge color="success">Approved</Badge>
<Badge tag="button" onclick={save} loading={saving}>Save filters</Badge>
<Badge onDelete={remove} deleteLabel="Remove priority filter" deleteLoading={removing}>
    Priority
</Badge>
```

Adjust the import path for your renderer. Keep Filament's theme, `@filamentStyles`, and `@filamentScripts` in your host layout (panels already include them). Script data supplies the PHP-computed Badge color classes, including contrast-aware text shades for registered custom palettes. The adapters do not add CSS. Without that script data, including server-side JavaScript rendering, text shades fall back to 700 / 200; custom palette parity requires the Filament host.

If you use Composer path symlinks to a Filament checkout with its own `node_modules`, configure Vite's `resolve.dedupe` with your framework (`['react', 'react-dom']`, `['vue']`, or `['svelte']`). Hooks and reactive effects must use the same framework instance as your application.

### Configuring appearance and native behavior

`color` defaults to `primary` and accepts registered color names, including `gray`. `size` defaults to `md`; `xs` and `sm` select the compact Blade variants. Other standard sizes retain Blade's medium styling. Pass a React node as `icon`, a Vue `#icon` slot, or a Svelte `icon` snippet. Icons use the existing Icon adapter; `iconPosition` is `before` (default) or `after`, and `iconSize` defaults to `sm` and accepts `xs`, `sm`, `md`, `lg`, `xl`, or `2xl`.

`tag` is `span` by default, or `a` / `button`. Native attributes, events, classes, and styles reach the root. Use `href` / `target` for links, and native `form="form-id"` / `type="submit"` for external forms; buttons otherwise default to `type="button"`. React forwards `ref` to the root element, Vue exposes `element` on its component ref, and Svelte supports `bind:element`. Keep `tag` stable to preserve the native element and child state.

`disabled` and `loading` block clicks and shortcuts. Loading replaces the decorative icon with LoadingIndicator and sets `aria-busy`; supply an appropriate visible label or host status announcement. The host starts and finishes loading explicitly. PHP icon aliases, Actions, Livewire target detection and loading delays, translations, SPA navigation, and router integration remain host responsibilities. No `wire:*` behavior is inferred.

### Deleting a badge

Pass `onDelete` to show the trailing native delete button. Use only `tag="span"` for deletable badges; interactive outer elements would create invalid nested controls and are rejected. Deletion does not bubble to the badge's click handler. `deleteLoading` disables this button and replaces its glyph with an extra-small LoadingIndicator. `deleteLabel` supplies its accessible name (default `Delete`); translate it in your host. A delete button takes precedence over an `after` icon, as in Blade. Remove the badge from your state when deletion succeeds.

### Adding tooltips and keyboard shortcuts

`tooltip` accepts plain text, never implicit HTML. It uses Tippy with the host's light/dark theme and dismisses on Escape. Disabled tooltip hosts remain focusable so keyboard users can read the explanation, but cannot activate; removing the tooltip restores native disabled behavior. For an otherwise non-focusable text badge, supply `tabIndex={0}` (React) or `tabindex="0"` (Vue/Svelte) when keyboard access to its tooltip is needed.

`keyBindings` accepts Mousetrap combinations or sequences, for example `['mod+shift+b']`. Like Blade's global bindings, they work while an input is focused, but do not activate a badge behind an open modal. Reserve combinations that do not conflict with browser, Alpine, or application shortcuts. When multiple JavaScript badges share a combination, the last registered eligible badge handles it; removing it restores the earlier registration. Removing bindings, disabling a badge, or unmounting it unregisters its shortcuts. Tooltip instances, theme observers, and Escape listeners are also released. All adapters share this behavior helper; they do not install Alpine directives inside framework-owned DOM.
