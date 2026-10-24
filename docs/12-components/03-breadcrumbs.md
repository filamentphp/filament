---
title: Breadcrumbs Blade component
---


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

<AutoScreenshot name="components/breadcrumbs/simple" alt="Breadcrumbs navigation" />

The keys of the array are URLs that the user is able to click on to navigate, and the values are the text that will be displayed for each link.

<Aside variant="danger">
    If you are using user-controlled URLs as array keys, validate their schemes using [`Str::sanitizeUrl()`](../advanced/security#validating-user-input) before rendering them. HTML escaping alone does not prevent a `javascript:` URL from executing code when clicked.
</Aside>
