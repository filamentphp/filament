---
title: Actions
---

## Getting started

### Single actions

Single actions may be registered in the `getTableActions()` method. Single action buttons are rendered at the end of each table row.

```php
protected function getTableActions(): array
{
    return [
        // ...
    ];
}
```

Actions may be created using the static `make()` method, passing its name. The name of the action should be unique. You can then pass a callback to `action()` which executes the task, or a callback to `url()` which generates a link URL:

> If you would like the URL to open in a new tab, you can use the `openUrlInNewTab()` method.

```php
use App\Models\Post;
use Filament\Tables\Actions\LinkAction;

LinkAction::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
```

### Bulk actions

Bulk actions may be registered in the `getTableBulkActions()` method. Bulk action buttons are visible when the user selects at least one record.

```php
protected function getTableBulkActions(): array
{
    return [
        // ...
    ];
}
```

Bulk actions may be created using the static `make()` method, passing its name. The name of the action should be unique. You should then pass a callback to `action()` which executes the task:

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
```

You may deselect the records after a bulk action has been executed using the `deselectRecordsAfterCompletion()` method:

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
```

## Setting a label

By default, the label of the action is generated from its name. You may customize this using the `label()` method:

```php
use App\Models\Post;
use Filament\Tables\Actions\LinkAction;

LinkAction::make('edit')
    ->label('Edit post')
    ->url(fn (Post $record): string => route('posts.edit', $record))
```

## Setting a color

Actions may have a color to indicate their significance. It may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
    ->color('danger')
```

## Setting an icon

Bulk actions and some single actions may also render a Blade icon component to indicate their purpose:

```php
use App\Models\Post;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Actions\IconButtonAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
    ->color('danger')
    ->icon('heroicon-o-trash')

ButtonAction::make('edit')
    ->label('Edit post')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->icon('heroicon-o-pencil')

IconButtonAction::make('edit')
    ->label('Edit post')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->icon('heroicon-o-pencil')
```

## Modals

Actions and bulk actions may require additional confirmation or form information before they run. With the table builder, you may open a modal before an action is executed to do this.

### Confirmation modals

You may require confirmation before an action is run using the `requiresConfirmation()` method. This is useful for particularly destructive actions, such as those that delete records.

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
    ->requiresConfirmation()
```

> Note: The confirmation modal is not available when a `url()` is set instead of an `action()`. Instead, you should redirect to the URL within the `action()` callback.

### Custom forms

You may also render a form in this modal to collect extra information from the user before the action runs.

You may use components from the [Form Builder](/docs/forms/fields) to create custom action modal forms. The data from the form is available in the `$data` array of the `action()` callback:

```php
use App\Modals\User;
use Filament\Forms;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('updateAuthor')
    ->action(function (Collection $records, array $data): void {
        foreach ($records as $record) {
            $record->author()->associate($data['authorId']);
            $record->save();
        }
    })
    ->form([
        Forms\Components\Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
```

### Setting a modal heading, subheading, and button label

You may customize the heading, subheading and button label of the modal:

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
    ->requiresConfirmation()
    ->modalHeading('Delete posts')
    ->modalSubheading('Are you sure you\'d like to delete these posts? This cannot be undone.')
    ->modalButton('Yes, delete them')
```

## Authorization

You may conditionally hide actions and bulk actions for certain users using the `hidden()` method, passing a closure:

```php
use App\Models\Post;
use Filament\Tables\Actions\LinkAction;

LinkAction::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->hidden(fn (Post $record): bool => auth()->user()->can('update', $record))
```

This is useful for authorization of certain actions to only users who have permission.
