---
title: Delete action
---

## Overview

Filament includes a prebuilt action that is able to delete Eloquent records. When the trigger button is clicked, a modal asks the user for confirmation. You may use it like so:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->record($this->post)
```

If you want to delete table rows, you can use the `Filament\Tables\Actions\DeleteAction` instead, or `Filament\Tables\Actions\DeleteBulkAction` to delete multiple at once:

```php
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            DeleteAction::make(),
            // ...
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                // ...
            ]),
        ]);
}
```

## Checking if a record is allowed to be deleted

In some cases, you may need to determine if a record can be deleted based on certain conditions. For example, you might want to prevent a user from being deleted if they are the owner of a "Team" model, to avoid integrity constraint violations.

You can use the `deletable()` method to run pre-deletion logic. This method accepts a callback and should return `true` if the record can be deleted, or `false` otherwise.

If the `deletable()` method returns `false`, the deletion process will be skipped and a notification will be displayed to the user. You can customize the title and body of this notification using the `notDeletableNotificationTitle()` and `notDeletableNotificationBody()` methods:

```php
DeleteAction::make()
    ->deletable(fn ($record) => $record->teams->isEmpty())
    ->notDeletableNotificationTitle('Cannot delete user')
    ->notDeletableNotificationBody(fn ($user) => "You cannot delete {$user->name} because they are the owner of one or more teams."),
```

## Redirecting after deleting

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
DeleteAction::make()
    ->successRedirectUrl(route('posts.list'))
```

## Customizing the delete notification

When the record is successfully deleted, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
DeleteAction::make()
    ->successNotificationTitle('User deleted')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Notifications\Notification;

DeleteAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User deleted')
            ->body('The user has been deleted successfully.'),
    )
```

To disable the notification altogether, use the `successNotification(null)` method:

```php
DeleteAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is deleted:

```php
DeleteAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```
