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

<AutoScreenshot name="components/loading-indicator/simple" alt="A loading indicator" version="5.x" />

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
