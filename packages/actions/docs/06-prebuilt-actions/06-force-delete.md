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

If you want to add this action to a row of a table, you may do so like this:

```php
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            ForceDeleteAction::make()
        ]);
}
```

Or if you want to add it as a table bulk action, so that the user can choose which rows to force delete, they can use `Filament\Actions\ForceDeleteBulkAction`:

```php
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            ForceDeleteBulkAction::make(),
        ]);
}
```

## Redirecting after force-deleting

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->successRedirectUrl(route('posts.list'))
```

## Customizing the force-delete notification

When the record is successfully force-deleted, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->successNotificationTitle('User force-deleted')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Actions\ForceDeleteAction;
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
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is force-deleted:

```php
use Filament\Actions\ForceDeleteAction;

ForceDeleteAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```
