---
title: Notifications
---

## Sending notifications

The admin panel uses the [Notifications](../notifications/sending-notifications) package to send messages to users.

Please read the [documentation](../notifications/sending-notifications) to discover how to send notifications easily.

However, there are a few differences in configuration when using the admin panel.

## Database notifications

Instead of enabling database notifications inside the notifications package, you must enable it for the admin panel specifically.

First, you must [publish the configuration file](installation#publishing-configuration) for the admin panel.

Now, enable database notifications:

```php
'database_notifications' => [
    'enabled' => true,
    // ...
],
```

You may also control [polling](../notifications/database-notifications#polling):

```php
'database' => [
    'enabled' => true,
    'polling_interval' => '30s',
    // ...
],
```

## Echo

Some features of the notifications package, including [receiving real-time database notifications](../notifications/database-notifications#echo) and [broadcast notifications](../notifications/broadcast-notifications), require Laravel Echo to be installed.

Firstly, you must set up a [server-side websockets integration](https://laravel.com/docs/broadcasting#server-side-installation) like Pusher.

Then, define your Echo configuration within the admin panel [configuration file](installation#publishing-configuration):

```php
'broadcasting' => [

    'echo' => [
        'broadcaster' => 'pusher',
        'key' => env('VITE_PUSHER_APP_KEY'),
        'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        'forceTLS' => true,
    ],
    
],
```
