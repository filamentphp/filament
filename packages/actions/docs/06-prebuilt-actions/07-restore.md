---
title: Restore action
---

## Overview

Filament includes a prebuilt action that is able to restore [soft deleted](https://laravel.com/docs/eloquent#soft-deleting) Eloquent records. When the trigger button is clicked, a modal asks the user for confirmation. You may use it like so:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->record($this->post)
```

If you want to add this action to a row of a table, you may do so like this:

```php
use Filament\Actions\RestoreAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            RestoreAction::make()
        ]);
}
```

Or if you want to add it as a table bulk action, so that the user can choose which rows to restore, they can use `Filament\Actions\RestoreBulkAction`:

```php
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            RestoreBulkAction::make(),
        ]);
}
```

## Redirecting after restoring

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->successRedirectUrl(route('posts.list'))
```

## Customizing the restore notification

When the record is successfully restored, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->successNotificationTitle('User restored')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;

RestoreAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User restored')
            ->body('The user has been restored successfully.'),
    )
```

To disable the notification altogether, use the `successNotification(null)` method:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is restored:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```
