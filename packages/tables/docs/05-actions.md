---
title: Actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

<LaracastsBanner
    title="Table Actions"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you the basics of adding actions to Filament resource tables."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/11"
    series="rapid-laravel-development"
/>

Filament's tables can use [Actions](../actions). They are buttons that can be added to the [end of any table row](#row-actions), or even in the [header](#header-actions) of a table. For instance, you may want an action to "create" a new record in the header, and then "edit" and "delete" actions on each row. [Bulk actions](#bulk-actions) can be used to execute code when records in the table are selected. Additionally, actions can be added to any [table column](#column-actions), such that each cell in that column is a trigger for your action.

It's highly advised that you read the documentation about [customizing action trigger buttons](../actions/trigger-button) and [action modals](../actions/modals) to that you are aware of the full capabilities of actions.

## Row actions

Action buttons can be rendered at the end of each table row. You can put them in the `$table->actions()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            // ...
        ]);
}
```

Actions may be created using the static `make()` method, passing its unique name.

You can then pass a function to `action()` which executes the task, or a function to `url()` which creates a link:

```php
use App\Models\Post;
use Filament\Tables\Actions\Action;

Action::make('edit')
    ->url(fn (Post $record): string => route('posts.edit', $record))
    ->openUrlInNewTab()

Action::make('delete')
    ->requiresConfirmation()
    ->action(fn (Post $record) => $record->delete())
```

All methods on the action accept callback functions, where you can access the current table `$record` that was clicked.

<AutoScreenshot name="tables/actions/simple" alt="Table with actions" version="3.x" />

### Positioning row actions before columns

By default, the row actions in your table are rendered in the final cell of each row. You may move them before the columns by using the `position` argument:

```php
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            // ...
        ], position: ActionsPosition::BeforeColumns);
}
```

<AutoScreenshot name="tables/actions/before-columns" alt="Table with actions before columns" version="3.x" />

### Positioning row actions before the checkbox column

By default, the row actions in your table are rendered in the final cell of each row. You may move them before the checkbox column by using the `position` argument:

```php
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            // ...
        ], position: ActionsPosition::BeforeCells);
}
```

<AutoScreenshot name="tables/actions/before-cells" alt="Table with actions before cells" version="3.x" />

### Accessing the selected table rows

You may want an action to be able to access all the selected rows in the table. Usually, this is done with a [bulk action](#bulk-actions) in the header of the table. However, you may want to do this with a row action, where the selected rows provide context for the action.

For example, you may want to have a row action that copies the row data to all the selected records. To force the table to be selectable, even if there aren't bulk actions defined, you need to use the `selectable()` method. To allow the action to access the selected records, you need to use the `accessSelectedRecords()` method. Then, you can use the `$selectedRecords` parameter in your action to access the selected records:

```php
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->selectable()
        ->actions([
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

Tables also support "bulk actions". These can be used when the user selects rows in the table. Traditionally, when rows are selected, a "bulk actions" button appears in the top left corner of the table. When the user clicks this button, they are presented with a dropdown menu of actions to choose from. You can put them in the `$table->bulkActions()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            // ...
        ]);
}
```

Bulk actions may be created using the static `make()` method, passing its unique name. You should then pass a callback to `action()` which executes the task:

```php
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->action(fn (Collection $records) => $records->each->delete())
```

The function allows you to access the current table `$records` that are selected. It is an Eloquent collection of models.

<AutoScreenshot name="tables/actions/bulk" alt="Table with bulk action" version="3.x" />

### Grouping bulk actions

You may use a `BulkActionGroup` object to [group multiple bulk actions together](../actions/grouping-actions) in a dropdown. Any bulk actions that remain outside the `BulkActionGroup` will be rendered next to the dropdown's trigger button:

```php
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
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
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
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
use Filament\Tables\Actions\BulkAction;
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
        ->bulkActions([
            // ...
        ])
        ->checkIfRecordIsSelectableUsing(
            fn (Model $record): bool => $record->status === Status::Enabled,
        );
}
```

### Preventing bulk-selection of all pages

The `selectCurrentPageOnly()` method can be used to prevent the user from easily bulk-selecting all records in the table at once, and instead only allows them to select one page at a time:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            // ...
        ])
        ->selectCurrentPageOnly();
}
```

## Header actions

Both [row actions](#row-actions) and [bulk actions](#bulk-actions) can be rendered in the header of the table. You can put them in the `$table->headerActions()` method:

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

<AutoScreenshot name="tables/actions/header" alt="Table with header actions" version="3.x" />

## Column actions

Actions can be added to columns, such that when a cell in that column is clicked, it acts as the trigger for an action. You can learn more about [column actions](columns/getting-started#running-actions) in the documentation.

## Prebuilt table actions

Filament includes several prebuilt actions and bulk actions that you can add to a table. Their aim is to simplify the most common Eloquent-related actions:

- [Create](../actions/prebuilt-actions/create)
- [Edit](../actions/prebuilt-actions/edit)
- [View](../actions/prebuilt-actions/view)
- [Delete](../actions/prebuilt-actions/delete)
- [Replicate](../actions/prebuilt-actions/replicate)
- [Force-delete](../actions/prebuilt-actions/force-delete)
- [Restore](../actions/prebuilt-actions/restore)
- [Import](../actions/prebuilt-actions/import)
- [Export](../actions/prebuilt-actions/export)

## Grouping actions

You may use an `ActionGroup` object to group multiple table actions together in a dropdown:

```php
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]),
            // ...
        ]);
}
```

<AutoScreenshot name="tables/actions/group" alt="Table with action group" version="3.x" />

### Choosing an action group button style

Out of the box, action group triggers have 3 styles - "button", "link", and "icon button".

"Icon button" triggers are circular buttons with an [icon](#setting-the-action-group-button-icon) and no label. Usually, this is the default button style, but you can use it manually with the `iconButton()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])->iconButton()
```

<AutoScreenshot name="tables/actions/group-icon-button" alt="Table with icon button action group" version="3.x" />

"Button" triggers have a background color, label, and optionally an [icon](#setting-the-action-group-button-icon). You can switch to that style with the `button()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])
    ->button()
    ->label('Actions')
```

<AutoScreenshot name="tables/actions/group-button" alt="Table with button action group" version="3.x" />

"Link" triggers have no background color. They must have a label and optionally an [icon](#setting-the-action-group-button-icon). They look like a link that you might find embedded within text. You can switch to that style with the `link()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])
    ->link()
    ->label('Actions')
```

<AutoScreenshot name="tables/actions/group-link" alt="Table with link action group" version="3.x" />

### Setting the action group button icon

You may set the [icon](https://blade-ui-kit.com/blade-icons?set=1#search) of the action group button using the `icon()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])->icon('heroicon-m-ellipsis-horizontal');
```

<AutoScreenshot name="tables/actions/group-icon" alt="Table with customized action group icon" version="3.x" />

### Setting the action group button color

You may set the color of the action group button using the `color()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])->color('info');
```

<AutoScreenshot name="tables/actions/group-color" alt="Table with customized action group color" version="3.x" />

### Setting the action group button size

Buttons come in 3 sizes - `sm`, `md` or `lg`. You may set the size of the action group button using the `size()` method:

```php
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])->size(ActionSize::Small);
```

<AutoScreenshot name="tables/actions/group-small" alt="Table with small action group" version="3.x" />

### Setting the action group tooltip

You may set the tooltip of the action group using the `tooltip()` method:

```php
use Filament\Tables\Actions\ActionGroup;

ActionGroup::make([
    // ...
])->tooltip('Actions');
```

<AutoScreenshot name="tables/actions/group-tooltip" alt="Table with action group tooltip" version="3.x" />

## Table action utility injection

All actions, not just table actions, have access to [many utilities](../actions/advanced#action-utility-injection) within the vast majority of configuration methods. However, in addition to those, table actions have access to a few more:

### Injecting the current Eloquent record

If you wish to access the current Eloquent record of the action, define a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (Model $record) {
    // ...
}
```

Be aware that bulk actions, header actions, and empty state actions do not have access to the `$record`, as they are not related to any table row.

### Injecting the current Eloquent model class

If you wish to access the current Eloquent model class of the table, define a `$model` parameter:

```php
function (string $model) {
    // ...
}
```

### Injecting the current table instance

If you wish to access the current table configuration instance that the action belongs to, define a `$table` parameter:

```php
use Filament\Tables\Table;

function (Table $table) {
    // ...
}
```
