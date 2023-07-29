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

If you want to restore table rows, you can use the `Filament\Tables\Actions\RestoreAction` instead, or `Filament\Tables\Actions\RestoreBulkAction` to restore multiple at once:

```php
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            RestoreAction::make(),
            // ...
        ])
        ->bulkActions([
            BulkActionGroup::make([
                RestoreBulkAction::make(),
                // ...
            ]),
        ]);
}
```

## Redirecting after restoring

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
RestoreAction::make()
    ->successRedirectUrl(route('posts.list'))
```

## Customizing the restore notification

When the record is successfully restored, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
RestoreAction::make()
    ->successNotificationTitle('User restored')
```

You may customize the entire notification using the `successNotification()` method:

```php
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
RestoreAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is restored:

```php
RestoreAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```
