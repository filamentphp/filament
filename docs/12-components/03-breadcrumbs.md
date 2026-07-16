---
title: Breadcrumbs Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The breadcrumbs component is used to render a simple, linear navigation that informs the user of their current location within the application:

```blade
<x-filament::breadcrumbs :breadcrumbs="[
    '/' => 'Home',
    '/dashboard' => 'Dashboard',
    '/dashboard/users' => 'Users',
    '/dashboard/users/create' => 'Create User',
]" />
```

<AutoScreenshot name="components/breadcrumbs/simple" alt="Breadcrumbs navigation" version="5.x" />

The keys of the array are URLs that the user is able to click on to navigate, and the values are the text that will be displayed for each link.
