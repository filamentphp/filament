---
title: Actions
---

## Getting started

### Single actions

Single action buttons are rendered at the end of each table row.

If you're using the actions in a Livewire component, you can put them in the `getTableActions()` method.

```php
protected function getTableActions(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them on the `$table`:

```php
public static function table(Table $table): Table
{
    return $table
        ->actions([
            // ...
        ]);
}
```

Actions may be created using the static `make()` method, passing its name. The name of the action should be unique. You can then pass a callback to `action()` which executes the task, or a callback to `url()` which generates a link URL:

> If you would like the URL to open in a new tab, you can use the `openUrlInNewTab()` method.

```php
use App\Models\Post;
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->openUrlInNewTab()
```

### Bulk actions

Bulk action buttons are visible when the user selects at least one record.

If you're using the actions in a Livewire component, you can put them in the `getTableBulkActions()` method.

```php
protected function getTableBulkActions(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them on the `$table`:

```php
public static function table(Table $table): Table
{
    return $table
        ->bulkActions([
            // ...
        ]);
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
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->label('Edit post')
    ->url(fn (Post $record): string => route('posts.edit', $record))
```

Optionally, you can have the label automatically translated by using the `translateLabel()` method:

```php
use App\Models\Post;
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->translateLabel() // Equivalent to `label(__('Edit'))`
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->action(fn (Collection $records) => $records->each->delete())
    ->deselectRecordsAfterCompletion()
    ->color('danger')
    ->icon('heroicon-o-trash')

Action::make('edit')
    ->label('Edit post')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->icon('heroicon-s-pencil')
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
use App\Models\User;
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

#### Filling default data

You may fill the form with default data, using the `mountUsing()` method:

```php
use App\Models\User;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;

Action::make('updateAuthor')
    ->mountUsing(fn (Forms\ComponentContainer $form, User $record) => $form->fill([
        'authorId' => $record->author->id,
    ]))
    ->action(function (User $record, array $data): void {
        $record->author()->associate($data['authorId']);
        $record->save();
    })
    ->form([
        Forms\Components\Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
```

#### Wizards

You may easily transform action forms into multistep wizards.

On the action, simply pass in the [wizard steps](../forms/layout#wizard) to the `steps()` method, instead of `form()`:

```php
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\Action;

Action::make('create')
    ->steps([
        Step::make('Name')
            ->description('Give the category a clear and unique name')
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->disabled()
                    ->required()
                    ->unique(Category::class, 'slug', fn ($record) => $record),
            ]),
        Step::make('Description')
            ->description('Add some extra details')
            ->schema([
                MarkdownEditor::make('description')
                    ->columnSpan('full'),
            ]),
        Step::make('Visibility')
            ->description('Control who can view it')
            ->schema([
                Toggle::make('is_visible')
                    ->label('Visible to customers.')
                    ->default(true),
            ]),
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

### Custom content

You may define custom content to be rendered inside your modal, which you can specify by passing a Blade view into the `modalContent()` method:

```php
use Filament\Tables\Actions\BulkAction;

BulkAction::make('advance')
    ->action(fn () => $this->record->advance())
    ->modalContent(view('filament.resources.event.actions.advance'))
```

## Authorization

You may conditionally show or hide actions and bulk actions for certain users using either the `visible()` or `hidden()` methods, passing a closure:

```php
use App\Models\Post;
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->visible(fn (Post $record): bool => auth()->user()->can('update', $record))
```

This is useful for authorization of certain actions to only users who have permission.

## Prebuilt actions

### Replicate

This package includes an action to replicate table records. You may use it like so:

```php
use Filament\Tables\Actions\ReplicateAction;

ReplicateAction::make()
```

The `excludeAttributes()` method is used to instruct the action which columns to be excluded from replication:

```php
use Filament\Tables\Actions\ReplicateAction;

ReplicateAction::make()->excludeAttributes(['slug'])
```

The `beforeReplicaSaved()` method can be used to invoke a Closure before saving the replica:

```php
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\Model;

ReplicateAction::make()
    ->beforeReplicaSaved(function (Model $replica): void {
        // ...
    })
```

The `afterReplicaSaved()` method can be used to invoke a Closure after saving the replica:

```php
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\Model;

ReplicateAction::make()
    ->afterReplicaSaved(function (Model $replica): void {
        // ...
    })
```

#### Retrieving user input

Just like [normal actions](#custom-forms), you can provide a [form schema](/docs/forms/fields) that can be used to modify the replication process:

```php
use Filament\Tables\Actions\ReplicateAction;

ReplicateAction::make()
	->excludeAttributes(['title'])
	->form([
		TextInput::make('title')->required(),
	])
	->beforeReplicaSaved(function (Model $replica, array $data): void {
		$replica->fill($data);
	})
```

## Grouping

You may use an `ActionGroup` object to group multiple table actions together in a dropdown:

```php
use Filament\Tables;

protected function getTableActions(): array
{
    return [
        Tables\Actions\ActionGroup::make([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]),
    ];
}
```

## Position

By default, the row actions in your table are rendered in the final cell. You may change the position by overriding the `getTableActionsPosition()` method:

```php
use Filament\Tables\Actions\Position;

protected function getTableActionsPosition(): ?string
{
    return Position::BeforeCells;
}
```

## Alignment

Row actions are aligned to the right in their cell by default. To change the alignment, update the configuration value inside of the package config:

```
'actions' => [
    'cell' => [
        'alignment' => 'right', // `right`, `left` or `center`
    ],
]
```

## Tooltips

> If you want to use tooltips outside of the admin panel, make sure you have [`@ryangjchandler/alpine-tooltip` installed](https://github.com/ryangjchandler/alpine-tooltip#installation) in your app, including [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager). You'll also need to install [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager) if you're using a [custom admin theme](/docs/admin/appearance#building-themes).

You may specify a tooltip to display when you hover over an action:

```php
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->tooltip('Edit this blog post')
```

This method also accepts a closure that can access the current table record:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

Action::make('edit')
    ->tooltip(fn (Model $record): string => "Edit {$record->title}")
```
