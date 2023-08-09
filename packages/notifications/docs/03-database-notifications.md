---
title: Database notifications
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Before we start, make sure that the [Laravel notifications table](https://laravel.com/docs/notifications#database-prerequisites) is added to your database:

```bash
php artisan notifications:table
```

> If you're using PostgreSQL, make sure that the `data` column in the migration is using `json()`: `$table->json('data')`.

> If you're using UUIDs for your `User` model, make sure that your `notifiable` column is using `uuidMorphs()`: `$table->uuidMorphs('notifiable')`.

To add database notifications to your app, you must add a new Livewire component to your Blade layout:

```blade
@livewire('database-notifications')
```

<AutoScreenshot name="notifications/database" alt="Database notifications" version="3.x" />

Database notifications will be rendered within a modal. To open this modal, you must have a "trigger" button in your view. Create a new trigger button component in your app, for instance at `/resources/views/notifications/database-notifications-trigger.blade.php`:

```blade
<button type="button">
    Notifications ({{ $unreadNotificationsCount }} unread)
</button>
```

`$unreadNotificationsCount` is a variable automatically passed to this view, which provides it with a real-time count of unread notifications the user has.

In the service provider, point to this new trigger view:

```php
use Filament\Notifications\Livewire\DatabaseNotifications;

DatabaseNotifications::trigger('filament-notifications.database-notifications-trigger');
```

Now, click on the trigger button that is rendered in your view. A modal should appear containing your database notifications when clicked!

## Sending database notifications

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

## Receiving database notifications

Without any setup, new database notifications will only be received when the page is first loaded.

### Polling for new database notifications

Polling is the practice of periodically making a request to the server to check for new notifications. This is a good approach as the setup is simple, but some may say that it is not a scalable solution as it increases server load.

By default, Livewire polls for new notifications every 30 seconds:

```php
use Filament\Notifications\Livewire\DatabaseNotifications;

DatabaseNotifications::databaseNotifications();
DatabaseNotifications::databaseNotificationsPollingInterval('30s');
```

You may completely disable polling if you wish:

```php
use Filament\Notifications\Livewire\DatabaseNotifications;

DatabaseNotifications::databaseNotifications();
DatabaseNotifications::databaseNotificationsPollingInterval(null);
```

### Using Echo to receive new database notifications with websockets

Alternatively, the package has a native integration with [Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation). Make sure Echo is installed, as well as a [server-side websockets integration](https://laravel.com/docs/broadcasting#server-side-installation) like Pusher.

Once websockets are set up, after sending a database notification you may dispatch a `DatabaseNotificationsSent` event, which will immediately fetch new notifications for that user:

```php
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;

$recipient = auth()->user();

Notification::make()
    ->title('Saved successfully')
    ->sendToDatabase($recipient);

event(new DatabaseNotificationsSent($recipient));
```

## Marking database notifications as read

There is a button at the top of the modal to mark all notifications as read at once. You may also add [Actions](sending-notifications#adding-actions-to-notifications) to notifications, which you can use to mark individual notifications as read. To do this, use the `markAsRead()` method on the action:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the post have been saved.')
    ->actions([
        Action::make('view')
            ->button()
            ->markAsRead(),
    ])
    ->send();
```

Alternatively, you may use the `markAsUnread()` method to mark a notification as unread:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the post have been saved.')
    ->actions([
        Action::make('markAsUnread')
            ->button()
            ->markAsUnread(),
    ])
    ->send();
```

## Opening the database notifications modal

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
