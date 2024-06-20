---
title: Notifications
---

## Overview

The Panel Builder uses the [Notifications](../notifications/sending-notifications) package to send messages to users. Please read the [documentation](../notifications/sending-notifications) to discover how to send notifications easily.

If you'd like to receive [database notifications](../notifications/database-notifications), you can enable them in the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->databaseNotifications();
}
```

You may also control database notification [polling](../notifications/database-notifications#polling-for-new-database-notifications):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->databaseNotifications()
        ->databaseNotificationsPolling('30s');
}
```

## Setting up websockets in a panel

The Panel Builder comes with a level of inbuilt support for real-time broadcast and database notifications. However there are a number of areas you will need to install and configure to wire everything up and get it working.

1. If you haven't already, read up on [broadcasting](https://laravel.com/docs/broadcasting) in the Laravel documentation.
2. Install and configure broadcasting to use a [server-side websockets integration](https://laravel.com/docs/broadcasting#server-side-installation) like Pusher.
3. If you haven't already, you will need to publish the Filament package configuration:

```bash
php artisan vendor:publish --tag=filament-config
```

4. Edit the configuration at `config/filament.php` and uncomment the `broadcasting.echo` section - ensuring the settings are correctly configured according to your broadcasting installation.
5. Ensure the [relevant `VITE_*` entries](https://laravel.com/docs/broadcasting#client-pusher-channels) exist in your `.env` file.
6. Clear relevant caches with `php artisan route:clear` and `php artisan config:clear` to ensure your new configuration takes effect.

Your panel should now be connecting to your broadcasting service. For example, if you log into the Pusher debug console you should see an incoming connection each time you load a page.

To send a real-time notification, see the [broadcast notifications documentation](../notifications/broadcast-notifications).
