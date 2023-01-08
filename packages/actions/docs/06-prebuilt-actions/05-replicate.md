---
title: Replicate action
---

## Overview

Filament includes a prebuilt action that is able to [replicate](https://laravel.com/docs/eloquent#replicating-models) Eloquent records. You may use it like so:

```php
use Filament\Actions\ReplicateAction;

ReplicateAction::make()
    ->record($this->post)
```

If you want to replicate table rows, you can use the `Filament\Tables\Actions\ReplicateAction` instead:

```php
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            ReplicateAction::make(),
            // ...
        ]);
}
```

## Excluding attributes

The `excludeAttributes()` method is used to instruct the action which columns to be excluded from replication:

```php
ReplicateAction::make()
    ->excludeAttributes(['slug'])
```

## Redirecting after replication

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
ReplicateAction::make()
    ->successRedirectUrl(route('posts.list'))
```

If you want to redirect using the replica, use the `$replica` parameter:

```php
use Illuminate\Database\Eloquent\Model;

ReplicateAction::make()
    ->successRedirectUrl(fn (Model $replica): string => route('posts.edit', [
        'post' => $replica,
    ]))
```

## Customizing the replicate notification

When the record is successfully replicated, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
ReplicateAction::make()
    ->successNotificationTitle('Category replicated')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Notifications\Notification;

ReplicateAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('Category replicated')
            ->body('The category has been replicated successfully.'),
    )
```

## Lifecycle hooks

Hooks may be used to execute code at various points within the action's lifecycle, like before the replica is saved.

```php
use Illuminate\Database\Eloquent\Model;

ReplicateAction::make()
    ->before(function () {
        // Runs before the record has been replicated.
    })
    ->beforeReplicaSaved(function (Model $replica): void {
        // Runs after the record has been replicated but before it is saved to the database.
    })
    ->after(function (Model $replica): void {
        // Runs after the replica has been saved to the database.
    })
```

## Halting the replication process

At any time, you may call `$action->halt()` from inside a lifecycle hook, which will halt the entire replication process:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

ReplicateAction::make()
    ->before(function (ReplicateAction $action) {
        if (! $this->record->team->subscribed()) {
            Notification::make()
                ->warning()
                ->title('You don\'t have an active subscription!')
                ->body('Choose a plan to continue.')
                ->persistent()
                ->actions([
                    Action::make('subscribe')
                        ->button()
                        ->url(route('subscribe'), shouldOpenInNewTab: true),
                ])
                ->send();
        
            $action->halt();
        }
    })
```

If you'd like the action modal to close too, you can completely `cancel()` the action instead of halting it:

```php
$action->cancel();
```