---
title: Force-delete action
---

## Overview

Filament includes a prebuilt action that is able to force-delete [soft deleted](https://laravel.com/docs/eloquent#soft-deleting) Eloquent records. When the trigger button is clicked, a modal asks the user for confirmation. You may use it like so:

```php
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->record($this->post)
```

If you want to force-delete table rows, you can use the `Filament\Tables\Actions\ForceDeleteAction` instead, or `Filament\Tables\Actions\ForceDeleteBulkAction` to force-delete multiple at once:

```php
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            ForceDeleteAction::make(),
            // ...
        ])
        ->bulkActions([
            BulkActionGroup::make([
                ForceDeleteBulkAction::make(),
                // ...
            ]),
        ]);
}
```

## Checking if a record is allowed to be force-deleted

In some cases, you may need to determine if a record can be force-deleted based on certain conditions. For example, you might want to prevent a user from being force-deleted if they are the owner of a "Team" model, to avoid integrity constraint violations.

You can use the `deletable()` method to run pre-deletion logic. This method accepts a callback and should return `true` if the record can be force-deleted, or `false` otherwise.

If the `deletable()` method returns `false`, the deletion process will be skipped and a notification will be displayed to the user. You can customize the title and body of this notification using the `notDeletableNotificationTitle()` and `notDeletableNotificationBody()` methods:

```php
ForceDeleteAction::make()
    ->deletable(fn ($record) => $record->teams->isEmpty())
    ->notDeletableNotificationTitle('Cannot delete user')
    ->notDeletableNotificationBody(fn ($user) => "You cannot delete {$user->name} because they are the owner of one or more teams."),
```

## Redirecting after force-deleting

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
ForceDeleteAction::make()
    ->successRedirectUrl(route('posts.list'))
```

## Customizing the force-delete notification

When the record is successfully force-deleted, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
ForceDeleteAction::make()
    ->successNotificationTitle('User force-deleted')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Notifications\Notification;

ForceDeleteAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User force-deleted')
            ->body('The user has been force-deleted successfully.'),
    )
```

To disable the notification altogether, use the `successNotification(null)` method:

```php
ForceDeleteAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is force-deleted:

```php
ForceDeleteAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```
