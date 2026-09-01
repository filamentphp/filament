---
title: Rendering notifications outside of a panel
---
import Aside from "@components/Aside.astro"

<Aside variant="warning">
    Before proceeding, make sure `filament/notifications` is installed in your project. You can check by running:

    ```bash
    composer show filament/notifications
    ```
    If it's not installed, consult the [installation guide](../introduction/installation#installing-the-individual-components) and configure the **individual components** according to the instructions.
</Aside>
## Introduction

To render notifications in your app, make sure the `notifications` Livewire component is rendered in your layout:

```blade
<div>
    @livewire('notifications')
</div>
```

Now, when [sending a notification](../notifications) from a Livewire request, it will appear for the user.
