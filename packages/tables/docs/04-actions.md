---
title: Actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Filament's tables can use [Actions](../actions). They are buttons that can be added to the [end of any table row](#record-actions), or even in the [header](#header-actions) or [toolbar](#toolbar-actions) of a table. For instance, you may want an action to "create" a new record in the header, and then "edit" and "delete" actions on each row. [Bulk actions](#bulk-actions) can be used to execute code when records in the table are selected. Additionally, actions can be added to any [table column](#column-actions), such that each cell in that column is a trigger for your action.

It's highly advised that you read the documentation about [customizing action trigger buttons](../actions/overview) and [action modals](../actions/modals) to that you are aware of the full capabilities of actions.

## Record actions

Action buttons can be rendered at the end of each table row. You can put them in the `$table->recordActions()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->recordActions([
            // ...
        ]);
}
```

Actions may be created using the static `make()` method, passing its unique name.

You can then pass a function to `action()` which executes the task, or a function to `url()` which creates a link:

```php
use App\Models\Post;
use Filament\Actions\Action;

Action::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->openUrlInNewTab()

Action::make('delete')
    ->requiresConfirmation()
    ->action(fn (Post $record) => $record->delete())
```

All methods on the action accept callback functions, where you can access the current table `$record` that was clicked.

<AutoScreenshot name="tables/actions/simple" alt="Table with actions" version="4.x" />

### Positioning record actions before columns

By default, the record actions in your table are rendered in the final cell of each row. You may move them before the columns by using the `position` argument:

```php
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->recordActions([
            // ...
        ], position: RecordActionsPosition::BeforeColumns);
}
```

<AutoScreenshot name="tables/actions/before-columns" alt="Table with actions before columns" version="4.x" />

### Positioning record actions before the checkbox column

By default, the record actions in your table are rendered in the final cell of each row. You may move them before the checkbox column by using the `position` argument:

```php
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->recordActions([
            // ...
        ], position: RecordActionsPosition::BeforeCells);
}
```

### Global record action settings

To customize the default configuration used for ungrouped record actions, you can use `modifyUngroupedRecordActionsUsing()` from a [`Table::configureUsing()` function](overview#global-settings) in the `boot()` method of a service provider:

```php
use Filament\Actions\Action;
use Filament\Tables\Table;

Table::configureUsing(function (Table $table): void {
    $table
        ->modifyUngroupedRecordActionsUsing(fn (Action $action) => $action->iconButton());
});
```

<AutoScreenshot name="tables/actions/before-cells" alt="Table with actions before cells" version="4.x" />

### Accessing the selected table rows

You may want an action to be able to access all the selected rows in the table. Usually, this is done with a [bulk action](#bulk-actions) in the header of the table. However, you may want to do this with a row action, where the selected rows provide context for the action.

For example, you may want to have a row action that copies the row data to all the selected records. To force the table to be selectable, even if there aren't bulk actions defined, you need to use the `selectable()` method. To allow the action to access the selected records, you need to use the `accessSelectedRecords()` method. Then, you can use the `$selectedRecords` parameter in your action to access the selected records:

```php
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->selectable()
        ->recordActions([
            Action::make('copyToSelected')
                ->accessSelectedRecords()
                ->action(function (Model $record, Collection $selectedRecords) {
                    $selectedRecords->each(
                        fn (Model $selectedRecord) => $selectedRecord->update([
                            'is_active' => $record->is_active,
                        ]),
                    );
                }),
        ]);
}
```

## Bulk actions

Tables also support "bulk actions". These can be used when the user selects rows in the table. Traditionally, when rows are selected, a "bulk actions" button appears. When the user clicks this button, they are presented with a dropdown menu of actions to choose from. You can put them in the `$table->toolbarActions()` or `$table->headerActions()` methods:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ]);
}
```

Bulk actions may be created using the static `make()` method, passing its unique name. You should then pass a callback to `action()` which executes the task:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->action(fn (Collection $records) => $records->each->delete())
```

The function allows you to access the current table `$records` that are selected. It is an Eloquent collection of models.

<AutoScreenshot name="tables/actions/bulk" alt="Table with bulk action" version="4.x" />

### Authorizing bulk actions

When using a bulk action, you may check a policy method for each record that is selected. This is useful for checking if the user has permission to perform the action on each record. You can use the `authorizeIndividualRecords()` method, passing the name of a policy method, which will be called for each record. If the policy denies authorization, the record will not be present in the bulk action's `$records` parameter:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->authorizeIndividualRecords('delete')
    ->action(fn (Collection $records) => $records->each->delete())
```

### Bulk action notifications

After a bulk action is completed, you may want to send a notification to the user with a summary of the action's success. This is especially useful if you're using [authorization](#authorizing-bulk-actions) for individual records, as the user may not know how many records were actually affected.

To send a notification after the bulk action is completed, you should set the `successNotificationTitle()` and `failureNotificationTitle()`:

- The `successNotificationTitle()` is used as the title of the notification when all records have been successfully processed.
- The `failureNotificationTitle()` is used as the title of the notification when some or all of the records failed to be processed. By passing a function to this methods, you can inject the `$successCount` and `$failureCount` parameters, to provide this information to the user.

For example:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->authorizeIndividualRecords('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->successNotificationTitle('Deleted users')
    ->failureNotificationTitle(function (int $successCount, int $totalCount): string {
        if ($successCount) {
            return "{$successCount} of {$totalCount} users deleted";
        }

        return 'Failed to delete any users';
    })
```

You can also use a special [authorization response object](https://laravel.com/docs/authorization#policy-responses) in a policy method to provide a custom message about why the authorization failed. The special object is called `DenyResponse` and replaces `Response::deny()`, allowing the developer to pass a function as the message which can receive information about how many records were denied by that authorization check:

```php
use App\Models\User;
use Filament\Support\Authorization\DenyResponse;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function delete(User $user, User $model): bool | Response
    {
        if (! $model->is_admin) {
            return true;
        }

        return DenyResponse::make('cannot_delete_admin', message: function (int $failureCount, int $totalCount): string {
            if (($failureCount === 1) && ($totalCount === 1)) {
                return 'You cannot delete an admin user.';
            }

            if ($failureCount === $totalCount) {
                return 'All users selected were admin users.';
            }

            if ($failureCount === 1) {
                return 'One of the selected users was an admin user.';
            }

            return "{$failureCount} of the selected users were admin users.";
        });
    }
}
```

The first argument to the `make()` method is a unique key to identify that failure type. If multiple failures of that key are detected, they are grouped together and only one message is generated. If there are multiple points of failure in the policy method, each response object can have its own key, and the messages will be concatenated together in the notification.

#### Reporting failures in bulk action processing

Alongside [individual record authorization](#authorizing-bulk-actions) messages, you can also report failures in the bulk action processing itself. This is useful if you want to provide a message for each record that failed to be processed for a particular reason, even after authorization passes. This is done by injecting the `Action` instance into the `action()` function, and calling the `reportBulkProcessingFailure()` method on it, passing a key and message function similar to `DenyResponse`:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->authorizeIndividualRecords('delete')
    ->action(function (BulkAction $action, Collection $records) {
        $records->each(function (Model $record) use ($action) {
            $record->delete() || $action->reportBulkProcessingFailure(
                'deletion_failed',
                message: function (int $failureCount, int $totalCount): string {
                    if (($failureCount === 1) && ($totalCount === 1)) {
                        return 'One user failed to delete.';
                    }
        
                    if ($failureCount === $totalCount) {
                        return 'All users failed to delete.';
                    }
        
                    if ($failureCount === 1) {
                        return 'One of the selected users failed to delete.';
                    }
        
                    return "{$failureCount} of the selected users failed to delete.";
                },
            );
        });
    })
    ->successNotificationTitle('Deleted users')
    ->failureNotificationTitle(function (int $successCount, int $totalCount): string {
        if ($successCount) {
            return "{$successCount} of {$totalCount} users deleted";
        }

        return 'Failed to delete any users';
    })
```

The `delete()` method on an Eloquent model returns `false` if the deletion fails, so you can use that to determine if the record was deleted successfully. The `reportBulkProcessingFailure()` method will then add a failure message to the notification, which will be displayed when the action is completed.

The `reportBulkProcessingFailure()` method can be called at multiple points during the action execution for different reasons, but you should only call it once per record. You should not proceed with the action for that particular record once you have called the method for it.

### Grouping bulk actions

You may use a `BulkActionGroup` object to [group multiple bulk actions together](../actions/grouping-actions) in a dropdown. Any bulk actions that remain outside the `BulkActionGroup` will be rendered next to the dropdown's trigger button:

```php
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            BulkActionGroup::make([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
                BulkAction::make('forceDelete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->forceDelete()),
            ]),
            BulkAction::make('export')->button()->action(fn (Collection $records) => ...),
        ]);
}
```

Alternatively, if all of your bulk actions are grouped, you can use the shorthand `groupedBulkActions()` method:

```php
use Filament\Actions\BulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groupedBulkActions([
            BulkAction::make('delete')
                ->requiresConfirmation()
                ->action(fn (Collection $records) => $records->each->delete()),
            BulkAction::make('forceDelete')
                ->requiresConfirmation()
                ->action(fn (Collection $records) => $records->each->forceDelete()),
        ]);
}
```

### Deselecting records once a bulk action has finished

You may deselect the records after a bulk action has been executed using the `deselectRecordsAfterCompletion()` method:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
```

### Disabling bulk actions for some rows

You may conditionally disable bulk actions for a specific record:

```php
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ])
        ->checkIfRecordIsSelectableUsing(
            fn (Model $record): bool => $record->status === Status::Enabled,
        );
}
```

### Limiting the number of selectable records

You may restrict how many records the user can select in total:

```php
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ])
        ->maxSelectableRecords(4);
}
```

### Preventing bulk-selection of all pages

The `selectCurrentPageOnly()` method can be used to prevent the user from easily bulk-selecting all records in the table at once, and instead only allows them to select one page at a time:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ])
        ->selectCurrentPageOnly();
}
```

### Restricting bulk selection to groups only

The `selectGroupsOnly()` method can be used to restrict bulk selection to only records within the same group, preventing bulk selection across multiple groups at once:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ])
        ->selectGroupsOnly();
}
```

### Improving the performance of bulk actions

By default, a bulk action will load all Eloquent records into memory before passing them to the `action()` function.

If you are processing a large number of records, you may want to use the `chunkSelectedRecords()` method to fetch a smaller number of records at a time. This will reduce the memory usage of your application:

```php
use Filament\Actions\BulkAction;
use Illuminate\Support\LazyCollection;

BulkAction::make()
    ->chunkSelectedRecords(250)
    ->action(function (LazyCollection $records) {
        // Process the records...
    })
```

You can still loop through the `$records` collection as normal, but the collection will be a `LazyCollection` instead of a normal collection.

You can also prevent Filament from fetching the Eloquent models in the first place, and instead just pass the IDs of the selected records to the `action()` function. This is useful if you are processing a large number of records, and you don't need to load them into memory:

```php
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;

BulkAction::make()
    ->fetchSelectedRecords(false)
    ->action(function (Collection $records) {
        // Process the records...
    })
```

## Header actions

Both actions and [bulk actions](#bulk-actions) can be rendered in the header of the table. You can put them in the `$table->headerActions()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->headerActions([
            // ...
        ]);
}
```

This is useful for things like "create" actions, which are not related to any specific table row, or bulk actions that need to be more visible.

<AutoScreenshot name="tables/actions/header" alt="Table with header actions" version="4.x" />

## Toolbar actions

Both actions and [bulk actions](#bulk-actions) can be rendered in the toolbar of the table. You can put them in the `$table->toolbarActions()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            // ...
        ]);
}
```

This is useful for things like "create" actions, which are not related to any specific table row, or bulk actions that need to be more visible.

<AutoScreenshot name="tables/actions/toolbar" alt="Table with toolbar actions" version="4.x" />

## Column actions

Actions can be added to columns, such that when a cell in that column is clicked, it acts as the trigger for an action. You can learn more about [column actions](columns/overview#triggering-actions) in the documentation.

## Grouping actions

You may use an `ActionGroup` object to group multiple table actions together in a dropdown:

```php
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->recordActions([
            ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]),
            // ...
        ]);
}
```

You may find out more about customizing action groups in the [Actions documentation](../actions/grouping-actions).

<AutoScreenshot name="tables/actions/group" alt="Table with action group" version="4.x" />
