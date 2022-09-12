---
title: Database notifications
---

> To start, make sure the package is [installed](installation) - `@livewire('notifications')` should be in your Blade layout somewhere.

Before we start, make sure that the [Laravel notifications table](https://laravel.com/docs/notifications#database-prerequisites) is added to your database:

```bash
php artisan notifications:table
```

> If you're using PostgreSQL, make sure that the `data` column in the migration is using `json()`: `$table->json('data')`.

> If you're using UUIDs for your `User` model, make sure that your `notifiable` column is using `uuidMorphs()`: `$table->uuidMorphs('notifiable')`.

First, you must [publish the configuration file](installation#publishing-configuration) for the package.

Inside the configuration file, there is a `database` key. To enable database notifications:

```php
'database' => [
    'enabled' => true,
    // ...
],
```

Database notifications will be rendered within a modal. To open this modal, you must have a "trigger" button in your view. Create a new trigger button component in your app, for instance at `/resources/views/notifications/database-notifications-trigger.blade.php`:

```blade
<button type="button">
    Notifications ({{ $unreadNotificationsCount }} unread)
</button>
```

`$unreadNotificationsCount` is a variable automatically passed to this view, which provides it with a real-time count of the number of unread notifications the user has.

In the configuration file, point to this new trigger view:

```php
'database' => [
    'enabled' => true,
    'trigger' => 'notifications.database-notifications-trigger',
    // ...
],
```

Now, simply move the `@livewire('notifications')` component to the position in your HTML that you wish to render the database notifications trigger button. It should appear, and open the database notifications modal when clicked!

## Sending notifications

There are several ways to send database notifications, depending on which one suits you best.

You may use our fluent API:

```php
use Filament\Notifications\Notification;

$recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->sendToDatabase($recipient);
```

Or, use the `notify()` method:

```php
use Filament\Notifications\Notification;

$recipient = auth()->user();

$recipient->notify(
    Notification::make()
        ->title('Saved successfully')
        ->toDatabase(),
);
```

Alternatively, use a traditional [Laravel notification class](https://laravel.com/docs/notifications#generating-notifications) by returning the notification from the `toDatabase()` method:

```php
use App\Models\User;
use Filament\Notifications\Notification;

public function toDatabase(User $notifiable): array
{
    return Notification::make()
        ->title('Saved successfully')
        ->getDatabaseMessage();
}
```

## Receiving notifications

Without any configuration, new database notifications will only be received when the page is first loaded.

### Polling

Polling is the practice of periodically making a request to the server to check for new notifications. This is a good approach as the setup is simple, but some may say that it is not a scalable solution as it increases server load.

By default, the configuration file polls for new notifications every 30 seconds:

```php
'database' => [
    'enabled' => true,
    'polling_interval' => '30s',
    // ...
],
```

You may completely disable polling if you wish:

```php
'database' => [
    'enabled' => true,
    'polling_interval' => null,
    // ...
],
```

### Echo

Alternatively, the package has a native integration with [Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation). Make sure Echo is installed, as well as a [server-side websockets integration](https://laravel.com/docs/broadcasting#server-side-installation) like Pusher.

Once websockets are set up, after sending a database notification you may emit a `DatabaseNotificationsSent` event, which will immediately fetch new notifications for that user:

```php
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;

$recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->sendToDatabase($recipient);

event(new DatabaseNotificationsSent($recipient));
```

## Opening the notifications modal

Instead of rendering the trigger button as described above, you can always open the database notifications modal from anywhere by dispatching an `open-modal` browser event:

```blade
<button
    x-data="{}"
    x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
    type="button"
>
    Notifications
</button>
```
