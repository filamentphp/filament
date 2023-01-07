---
title: Force-delete action
---

## Overview

Filament includes a prebuilt action that is able to force-delete [soft deleted](https://laravel.com/docs/eloquent#soft-deleting) Eloquent records. When the trigger button is clicked, a modal asking the user for confirmation. You may use it like so:

```php
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->record($this->post)
```

If you want to force-delete table rows, you can use the `Filament\Tables\Actions\ForceDeleteAction` instead, or `Filament\Tables\Actions\ForceDeleteBulkAction` to force-delete more than one at once:

```php
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
            ForceDeleteBulkAction::make(),
            // ...
        ]);
}
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