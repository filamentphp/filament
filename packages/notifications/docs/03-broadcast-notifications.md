---
title: Broadcast notifications
---

## Introduction

By default, Filament will send flash notifications via the Laravel session. However, you may wish that your notifications are "broadcast" to a user in real-time, instead. This could be used to send a temporary success notification from a queued job after it has finished processing.

We have a native integration with [Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation). Make sure Echo is installed, as well as a [server-side websockets integration](https://laravel.com/docs/broadcasting#server-side-installation) like Pusher.

## Sending broadcast notifications

There are several ways to send broadcast notifications, depending on which one suits you best.

You may use our fluent API:

```php
use Filament\Notifications\Notification;

$recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->broadcast($recipient);
```

Or, use the `notify()` method:

```php
use Filament\Notifications\Notification;

$recipient = auth()->user();

$recipient->notify(
    Notification::make()
        ->title('Saved successfully')
        ->toBroadcast(),
)
```

Alternatively, use a traditional [Laravel notification class](https://laravel.com/docs/notifications#generating-notifications) by returning the notification from the `toBroadcast()` method:

```php
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

public function toBroadcast(User $notifiable): BroadcastMessage
{
    return Notification::make()
        ->title('Saved successfully')
        ->getBroadcastMessage();
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
