---
title: Rendering a widget in a Blade view
---
import Aside from "@components/Aside.astro"

<Aside variant="warning">
    Before proceeding, make sure `filament/widgets` is installed in your project. You can check by running:

    ```bash
    composer show filament/widgets
    ```
    If it's not installed, consult the [installation guide](../introduction/installation#installing-the-individual-components) and configure the **individual components** according to the instructions.
</Aside>

## Creating a widget

Use the `make:filament-widget` command to generate a new widget. For details on customization and usage, see the [widgets section](../widgets).

## Adding the widget

Since widgets are Livewire components, you can easily render a widget in any Blade view using the `@livewire` directive:

```blade
<div>
    @livewire(\App\Livewire\Dashboard\PostsChart::class)
</div>
```

<Aside variant="info">
    If you're using a [table widget](../widgets/overview#table-widgets), make sure to install `filament/tables` as well.  
    Refer to the [installation guide](../introduction/installation#installing-the-individual-components) and follow the steps to configure the **individual components** properly.
</Aside>
