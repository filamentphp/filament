---
title: Managing relationships
---

## Choosing the right tool for the job

Filament provides many ways to manage relationships in the app. Which feature you should use depends on the type of relationship you are managing, and which UI you are looking for.

### Relation managers - interactive tables underneath your resource forms

> These are compatible with `HasMany`, `HasManyThrough`, `BelongsToMany`, `MorphMany` and `MorphToMany` relationships.

[Relation managers](#creating-a-relation-manager) are interactive tables that allow administrators to list, create, attach, associate, edit, detach, dissociate and delete related records without leaving the resource's Edit or View page.

### Select & checkbox list - choose from existing records or create a new one

> These are compatible with `BelongsTo`, `MorphTo` and `BelongsToMany` relationships.

Using a [select](../../forms/fields/select#integrating-with-an-eloquent-relationship), users will be able to choose from a list of existing records. You may also [add a button that allows you to create a new record inside a modal](../../forms/fields/select#creating-new-records), without leaving the page.

When using a `BelongsToMany` relationship with a select, you'll be able to select multiple options, not just one. Records will be automatically added to your pivot table when you submit the form. If you wish, you can swap out the multi-select dropdown with a simple [checkbox list](../../forms/fields/checkbox-list#integrating-with-an-eloquent-relationship). Both components work in the same way.

### Repeaters - CRUD multiple related records inside the owner's form

> These are compatible with `HasMany` and `MorphMany` relationships.

[Repeaters](../../forms/fields/repeater#integrating-with-an-eloquent-relationship) are standard form components, which can render a repeatable set of fields infinitely. They can be hooked up to a relationship, so records are automatically read, created, updated, and deleted from the related table. They live inside the main form schema, and can be used inside resource pages, as well as nesting within action modals.

From a UX perspective, this solution is only suitable if your related model only has a few fields. Otherwise, the form can get very long.

### Layout form components - saving form fields to a single relationship

> These are compatible with `BelongsTo`, `HasOne` and `MorphOne` relationships.

All layout form components ([Grid](../../forms/layout/grid#grid-component), [Section](../../forms/layout/section), [Fieldset](../../forms/layout/fieldset), [Card](../../forms/layout/card), etc) have a `relationship()` method. When you use this, all fields within that layout are saved to the related model instead of the owner's model:

```php
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

Fieldset::make('Metadata')
    ->relationship('metadata')
    ->schema([
        TextInput::make('title'),
        Textarea::make('description'),
        FileUpload::make('image'),
    ])
```

In this example, the `title`, `description` and `image` are automatically loaded from the `metadata` relationship, and saved again when the form is submitted. If the `metadata` record does not exist, it is automatically created.

## Creating a relation manager

To create a relation manager, you can use the `make:filament-relation-manager` command:

```bash
php artisan make:filament-relation-manager CategoryResource posts title
```

- `CategoryResource` is the name of the resource class for the owner (parent) model.
- `posts` is the name of the relationship you want to manage.
- `title` is the name of the attribute that will be used to identify posts.

This will create a `CategoryResource/RelationManagers/PostsRelationManager.php` file. This contains a class where you are able to define a [form](getting-started#forms) and [table](getting-started#table) for your relation manager:

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('title')->required(),
            // ...
        ]);
}

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title'),
            // ...
        ]);
}
```

You must register the new relation manager in your resource's `getRelations()` method:

```php
public static function getRelations(): array
{
    return [
        RelationManagers\PostsRelationManager::class,
    ];
}
```

Once a table and form have been defined for the relation manager, visit the [Edit](editing-records) or [View](viewing-records) page of your resource to see it in action.

### Unconventional inverse relationship names

For inverse relationships that do not follow Laravel's naming guidelines, you may wish to use the `inverseRelationship()` method on the table:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title'),
            // ...
        ])
        ->inverseRelationship('section'); // Since the inverse related model is `Category`, this is normally `category`, not `section`.
}
```

### Handling soft deletes

By default, you will not be able to interact with deleted records in the relation manager. If you'd like to add functionality to restore, force delete and filter trashed records in your relation manager, use the `--soft-deletes` flag when generating the relation manager:

```bash
php artisan make:filament-relation-manager CategoryResource posts title --soft-deletes
```

You can find out more about soft deleting [here](#deleting-records).

## Listing related records

Related records will be listed in a table. The entire relation manager is based around this table, which contains actions to [create](#creating-records), [edit](#editing-records), [attach / detach](#attaching-and-detaching-records), [associate / dissociate](#associating-and-dissociating-records), and delete records.

As per the documentation on [listing records](listing-records), you may use all the same utilities for customization on the relation manager:

- [Columns](listing-records#columns)
- [Filters](listing-records#filters)
- [Actions](listing-records#actions)
- [Bulk actions](listing-records#bulk-actions)

Additionally, you may use any other feature of the [table builder](../../tables).

### Listing with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also add pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the table, you can use:

```php
use Filament\Tables;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('role'),
        ]);
}
```

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

## Creating related records

### Creating with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also add pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the create form, you can use:

```php
use Filament\Forms;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('role')->required(),
            // ...
        ]);
}
```

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

### Customizing the `CreateAction`

To learn how to customize the `CreateAction`, including mutating the form data, changing the notification, and adding lifecycle hooks, please see the [actions documentation](../../actions/prebuilt-actions/create).

## Editing related records

### Editing with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also edit pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the edit form, you can use:

```php
use Filament\Forms;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('role')->required(),
            // ...
        ]);
}
```

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

### Customizing the `EditAction`

To learn how to customize the `EditAction`, including mutating the form data, changing the notification, and adding lifecycle hooks, please see the [actions documentation](../../actions/prebuilt-actions/edit).

## Attaching and detaching records

Filament is able to attach and detach records for `BelongsToMany` and `MorphToMany` relationships.

When generating your relation manager, you may pass the `--attach` flag to also add `AttachAction`, `DetachAction` and `DetachBulkAction` to the table:

```bash
php artisan make:filament-relation-manager CategoryResource posts title --attach
```

Alternatively, if you've already generated your resource, you can just add the actions to the `$table` arrays:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->headerActions([
            // ...
            Tables\Actions\AttachAction::make(),
        ])
        ->actions([
            // ...
            Tables\Actions\DetachAction::make(),
        ])
        ->bulkActions([
            // ...
            Tables\Actions\DetachBulkAction::make(),
        ]);
}
```

### Preloading the attachment modal select options

By default, as you search for a record to attach, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `preloadRecordSelect()` method of `AttachAction`:

```php
use Filament\Tables\Actions\AttachAction;

AttachAction::make()->preloadRecordSelect()
```

### Attaching with pivot attributes

When you attach record with the `Attach` button, you may wish to define a custom form to add pivot attributes to the relationship:

```php
use Filament\Forms;
use Filament\Tables\Actions\AttachAction;

AttachAction::make()
    ->form(fn (AttachAction $action): array => [
        $action->getRecordSelect(),
        Forms\Components\TextInput::make('role')->required(),
    ])
```

In this example, `$action->getRecordSelect()` outputs the select field to pick the record to attach. The `role` text input is then saved to the pivot table's `role` column.

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

### Scoping the options

You may want to scope the options available to `AttachAction`:

```php
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

AttachAction::make()
    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(auth()->user())
```

### Handling duplicates

By default, you will not be allowed to attach a record more than once. This is because you must also set up a primary `id` column on the pivot table for this feature to work.

Please ensure that the `id` attribute is listed in the `withPivot()` method of the relationship *and* inverse relationship.

Finally, add the `$allowsDuplicates` property to the relation manager:

```php
protected bool $allowsDuplicates = true;
```

## Associating and dissociating records

Filament is able to associate and dissociate records for `HasMany`, `HasManyThrough` and `MorphMany` relationships.

When generating your relation manager, you may pass the `--associate` flag to also add `AssociateAction`, `DissociateAction` and `DissociateBulkAction` to the table:

```bash
php artisan make:filament-relation-manager CategoryResource posts title --associate
```

Alternatively, if you've already generated your resource, you can just add the actions to the `$table` arrays:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->headerActions([
            // ...
            Tables\Actions\AssociateAction::make(),
        ])
        ->actions([
            // ...
            Tables\Actions\DissociateAction::make(),
        ])
        ->bulkActions([
            // ...
            Tables\Actions\DissociateBulkAction::make(),
        ]);
}
```

### Preloading the associate modal select options

By default, as you search for a record to associate, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `preloadRecordSelect()` method of `AssociateAction`:

```php
use Filament\Tables\Actions\AssociateAction;

AssociateAction::make()->preloadRecordSelect()
```

### Scoping the options

You may want to scope the options available to `AssociateAction`:

```php
use Filament\Tables\Actions\AssociateAction;
use Illuminate\Database\Eloquent\Builder;

AssociateAction::make()
    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(auth()->user())
```

## Viewing related records

When generating your relation manager, you may pass the `--view` flag to also add a `ViewAction` to the table:

```bash
php artisan make:filament-relation-manager CategoryResource posts title --view
```

Alternatively, if you've already generated your relation manager, you can just add the `ViewAction` to the `$table->actions()` array:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            // ...
        ]);
}
```

## Deleting related records

By default, you will not be able to interact with deleted records in the relation manager. If you'd like to add functionality to restore, force delete and filter trashed records in your relation manager, use the `--soft-deletes` flag when generating the relation manager:

```bash
php artisan make:filament-relation-manager CategoryResource posts title --soft-deletes
```

Alternatively, you may add soft deleting functionality to an existing relation manager:

```php
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
            // ...
        ])
        ->actions([
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
            // ...
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
            Tables\Actions\ForceDeleteBulkAction::make(),
            Tables\Actions\RestoreBulkAction::make(),
            // ...
        ]);
}

protected function getTableQuery(): Builder
{
    return parent::getTableQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
}
```

### Customizing the `DeleteAction`

To learn how to customize the `DeleteAction`, including changing the notification and adding lifecycle hooks, please see the [actions documentation](../../actions/prebuilt-actions/delete).

## Accessing the relationship's owner record

Relation managers are Livewire components. When they are first loaded, the owner record (the Eloquent record which serves as a parent - the main resource model) is mounted in a public `$ownerRecord` property. Thus, you may access the owner record using:

```php
$this->ownerRecord
```

However, in you're inside a `static` method like `form()` or `table()`, `$this` isn't accessible. So, you may [use a callback](../../forms/advanced#using-closure-customization) to access the `$livewire` instance:

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('store_id')
                ->options(function (RelationManager $livewire): array {
                    return $livewire->ownerRecord->stores()
                        ->pluck('name', 'id')
                        ->toArray();
                }),
            // ...
        ]);
}
```

All methods in Filament accept a callback which you can access `$livewire->ownerRecord` in.

## Grouping relation managers

You may choose to group relation managers together into one tab. To do this, you may wrap multiple managers in a `RelationGroup` object, with a label:

```php
use Filament\Resources\RelationManagers\RelationGroup;

public static function getRelations(): array
{
    return [
        // ...
        RelationGroup::make('Contacts', [
            RelationManagers\IndividualsRelationManager::class,
            RelationManagers\OrganizationsRelationManager::class,
        ]),
        // ...
    ];
}
```

## Conditionally showing relation managers

By default, relation managers will be visible if the `viewAny()` method for the related model policy returns `true`.

You may use the `canView()` method to determine if the relation manager should be visible for a specific owner record and page:

```php
use Illuminate\Database\Eloquent\Model;

public static function canView(Model $ownerRecord, string $pageClass): bool
{
    return $ownerRecord->status === Status::Draft;
}
```

## Combining the relation manager tabs with the form

On the Edit or View page class, override the `hasCombinedRelationManagerTabsWithContent()` method:

```php
public function hasCombinedRelationManagerTabsWithContent(): bool
{
    return true;
}
```
