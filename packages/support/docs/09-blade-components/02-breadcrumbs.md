---
title: Breadcrumbs Blade component
---

## Overview

The breadcrumbs component is used to render a simple, linear navigation that informs the user of their current location within the application:

```blade
<x-filament::breadcrumbs :breadcrumbs="[
    '/' => 'Home',
    '/dashboard' => 'Dashboard',
    '/dashboard/users' => 'Users',
    '/dashboard/users/create' => 'Create User',
]" />
```

The keys of the array are URLs that the user is able to click on to navigate, and the values are the text that will be displayed for each link.

### RTL Support

If you need to rotate the breadcrumb separator for RTL support, simply add the `dir` attribute with the value of `rtl`:

```
<x-filament::breadcrumbs 
    :breadcrumbs="[
        '/' => 'Home',
        '/dashboard' => 'Dashboard',
    ]"
    dir="rtl"
/>
```

By default the breadcrumb separator direction is set to `ltr`, and will look similar to the following:

```
Home > Dashboard > Users
```

If you specify `rtl` as the direction, the breadcrumbs will look similar to the following:

```
Home < Dashboard < Users
```
