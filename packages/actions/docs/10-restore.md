---
title: Restore action
---
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Filament includes an action that is able to restore [soft-deleted](https://laravel.com/docs/eloquent#soft-deleting) Eloquent records. When the trigger button is clicked, a modal asks the user for confirmation. You may use it like so:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
```

Or if you want to add it as a table bulk action, so that the user can choose which rows to restore, use `Filament\Actions\RestoreBulkAction`:

```php
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
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

<UtilityInjection set="actions" version="4.x">As well as `$record`, the `successRedirectUrl()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the restore notification

When the record is successfully restored, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\RestoreAction;

RestoreAction::make()
    ->successNotificationTitle('User restored')
```

<UtilityInjection set="actions" version="4.x">As well as allowing a static value, the `successNotificationTitle()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

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

<UtilityInjection set="actions" version="4.x" extras="Notification;;Filament\Notifications\Notification;;$notification;;The default notification object, which could be a useful starting point for customization.">As well as allowing a static value, the `successNotification()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

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

<UtilityInjection set="actions" version="4.x">These hook functions can inject various utilities as parameters.</UtilityInjection>

## Improving the performance of restore bulk actions

By default, the `RestoreBulkAction` will load all Eloquent records into memory, before looping over them and restoring them one by one.

If you are restoring a large number of records, you may want to use the `chunkSelectedRecords()` method to fetch a smaller number of records at a time. This will reduce the memory usage of your application:

```php
use Filament\Actions\RestoreBulkAction;

RestoreBulkAction::make()
    ->chunkSelectedRecords(250)
```

Filament loads Eloquent records into memory before restoring them for two reasons:

- To allow individual records in the collection to be authorized with a model policy before restoration (using `authorizeIndividualRecords('restore')`, for example).
- To ensure that model events are run when restoring records, such as the `restoring` and `restored` events in a model observer.

If you do not require individual record policy authorization and model events, you can use the `fetchSelectedRecords(false)` method, which will not fetch the records into memory before restoring them, and instead will restore them in a single query:

```php
use Filament\Actions\RestoreBulkAction;

RestoreBulkAction::make()
    ->fetchSelectedRecords(false)
```
