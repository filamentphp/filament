---
title: Loading indicator Blade component
---

import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The loading indicator is an animated SVG that can be used to indicate that something is in progress:

```blade
<x-filament::loading-indicator class="h-5 w-5" />
```

<AutoScreenshot name="components/loading-indicator/simple" alt="A loading indicator" version="4.x" />

## Using JavaScript components

You can import the default loading indicator into React, Vue, or Svelte renderers. These source components use your application's existing framework build and Filament theme CSS; they do not require converting your application to TypeScript. The examples assume your component is in `resources/js/`; adjust the import path for other directories.

```jsx
import React from 'react'
import LoadingIndicator from '../../vendor/filament/support/resources/js/react/LoadingIndicator'

<div role="status" aria-label="Loading results">
    <LoadingIndicator size="sm" />
</div>
```

```vue
<script setup>
import LoadingIndicator from '../../vendor/filament/support/resources/js/vue/LoadingIndicator.vue'
</script>

<template>
    <div role="status" aria-label="Loading results">
        <LoadingIndicator size="sm" />
    </div>
</template>
```

```svelte
<script>
    import LoadingIndicator from '../../vendor/filament/support/resources/js/svelte/LoadingIndicator.svelte'
</script>

<div role="status" aria-label="Loading results">
    <LoadingIndicator size="sm" />
</div>
```

Adjust the import paths to your Composer vendor directory. The typed `size` prop accepts `xs`, `sm`, `md` (default), `lg`, `xl`, and `2xl`, matching `IconSize::ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, and `TwoExtraLarge` respectively. These map to the existing `fi-size-*` hooks alongside `fi-icon fi-loading-indicator`. In PHP, `generate_loading_indicator_html()` takes an `IconSize` argument; the Blade wrapper only forwards attributes and does not interpret a `size` prop.

Native SVG attributes and events are forwarded, including overrides for `aria-hidden`, `fill`, and `viewBox`. Use `className` in React and `class` in Vue or Svelte to add classes. React also forwards a ref to the `SVGSVGElement`. The component owns its two paths and does not accept children, slots, or raw HTML. Animation comes only from Filament's existing motion-safe CSS and stops when the user prefers reduced motion.

The SVG is decorative (`aria-hidden="true"`) by default. Your host should convey the loading state through a labelled control, a status region, or `aria-busy` on the affected content. If you expose the SVG itself, override `aria-hidden` and provide an accessible name and role.

JavaScript components always render the default SVG; they do not resolve PHP container bindings. If your application replaces the loading indicator, compose or replace the component in your host renderer as well.

## Replacing the default loading indicator

Filament renders the loading indicator through the `Filament\Support\Contracts\LoadingIndicator` contract, which is bound to `Filament\Support\View\DefaultLoadingIndicator` by default. You may replace it with your own implementation by binding a different class in a service provider:

```php
use App\Support\CustomLoadingIndicator;
use Filament\Support\Contracts\LoadingIndicator;

public function register(): void
{
    $this->app->bind(LoadingIndicator::class, CustomLoadingIndicator::class);
}
```

Your class must implement the `LoadingIndicator` contract, whose `toHtml()` method receives a `ComponentAttributeBag` and returns the indicator's HTML:

```php
namespace App\Support;

use Filament\Support\Contracts\LoadingIndicator;
use Illuminate\View\ComponentAttributeBag;

class CustomLoadingIndicator implements LoadingIndicator
{
    public function toHtml(ComponentAttributeBag $attributes): string
    {
        return <<<HTML
            <svg {$attributes->toHtml()}>
                <!-- ... -->
            </svg>
        HTML;
    }
}
```

The attributes already contain the `fi-icon fi-loading-indicator` and size hook classes, so you can forward them directly to your root element.

<Aside variant="warning">
    The resolved `LoadingIndicator` instance is cached for the lifetime of the PHP process. Under Laravel Octane, this means the binding is only resolved once when the worker boots and will not be re-resolved between requests. Register your binding in a service provider rather than rebinding it at runtime.
</Aside>
