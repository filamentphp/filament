---
title: Adding a widget to a Blade view
---

## Overview

Since widgets are Livewire components, you can easily render a widget in any Blade view using the `@livewire` directive:

```blade
<div>
    @livewire(\App\Livewire\Dashboard\PostsChart::class)
</div>
```
