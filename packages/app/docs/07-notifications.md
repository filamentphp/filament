---
title: Notifications
---

## Overview

The app framework uses the [Notifications](../notifications/sending-notifications) package to send messages to users. Please read the [documentation](../notifications/sending-notifications) to discover how to send notifications easily.

If you'd like to receive [database notifications](../notifications/database-notifications), you can enable them in the [configuration](configuration):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->databaseNotifications();
}
```

You may also control database notification [polling](../notifications/database-notifications#polling-for-new-database-notifications):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->databaseNotifications()
        ->databaseNotificationsPolling('30s');
}
```
