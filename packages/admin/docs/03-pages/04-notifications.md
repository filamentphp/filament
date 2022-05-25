---
title: Notifications
---

## Sending notifications

You can send flash notifications to the user from each page by calling the `notify()` method on the page class:

```php
$this->notify('success', 'Saved');
```

## Types of notifications

There are four types of notifications available, each with a different color and icon:

- `primary` - for providing information.
- `danger` - for reporting errors.
- `success` - for success messages.
- `warning` - for reporting non-critical issues.

## Sending notifications from outside the page class

Alternatively, you can call `Filament::notify()` from anywhere in your app, and pass the same arguments:

```php
use Filament\Facades\Filament;

Filament::notify('success', 'Saved');
```
