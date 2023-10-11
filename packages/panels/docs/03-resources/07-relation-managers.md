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

All layout form components ([Grid](../../forms/layout/grid#grid-component), [Section](../../forms/layout/section), [Fieldset](../../forms/layout/fieldset), etc.) have a `relationship()` method. When you use this, all fields within that layout are saved to the related model instead of the owner's model:

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

This will create a `CategoryResource/RelationManagers/PostsRelationManager.php` file. This contains a class where you are able to define a [form](getting-started#resource-forms) and [table](getting-started#resource-tables) for your relation manager:

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

### Read-only mode

Relation managers are usually displayed on either the Edit or View page of a resource. On the View page, Filament will automatically hide all actions that modify the relationship, such as create, edit, and delete. We call this "read-only mode", and it is there by default to preserve the read-only behaviour of the View page. However, you can disable this behaviour, by overriding the `isReadOnly()` method on the relation manager class to return `false` all the time:

```php
public function isReadOnly(): bool
{
    return false;
}
```

Alternatively, if you hate this functionality, you can disable it for all relation managers at once in the panel [configuration](../configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->readOnlyRelationManagersOnResourceViewPagesByDefault(false);
}
```

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

Related records will be listed in a table. The entire relation manager is based around this table, which contains actions to [create](#creating-related-records), [edit](#editing-related-records), [attach / detach](#attaching-and-detaching-records), [associate / dissociate](#associating-and-dissociating-records), and delete records.

You may use any features of the [Table Builder](../../tables) to customize relation managers.

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

To learn how to customize the `CreateAction`, including mutating the form data, changing the notification, and adding lifecycle hooks, please see the [Actions documentation](../../actions/prebuilt-actions/create).

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

To learn how to customize the `EditAction`, including mutating the form data, changing the notification, and adding lifecycle hooks, please see the [Actions documentation](../../actions/prebuilt-actions/edit).

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
            Tables\Actions\BulkActionGroup::make([
                // ...
                Tables\Actions\DetachBulkAction::make(),
            ]),
        ]);
}
```

### Preloading the attachment modal select options

By default, as you search for a record to attach, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `preloadRecordSelect()` method of `AttachAction`:

```php
use Filament\Tables\Actions\AttachAction;

AttachAction::make()
    ->preloadRecordSelect()
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

In this example, `$action->getRecordSelect()` returns the select field to pick the record to attach. The `role` text input is then saved to the pivot table's `role` column.

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

### Scoping the options to attach

You may want to scope the options available to `AttachAction`:

```php
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

AttachAction::make()
    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(auth()->user())
```

### Searching the options to attach across multiple columns

By default, the options available to `AttachAction` will be searched in the `recordTitleAttribute()` of the table. If you wish to search across multiple columns, you can use the `recordSelectSearchColumns()` method:

```php
use Filament\Tables\Actions\AttachAction;

AttachAction::make()
    ->recordSelectSearchColumns(['title', 'description'])
```

### Customizing the select field in the attached modal

You may customize the select field object that is used during attachment by passing a function to the `recordSelect()` method:

```php
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;

AttachAction::make()
    ->recordSelect(
        fn (Select $select) => $select->placeholder('Select a post'),
    )
```

### Handling duplicates

By default, you will not be allowed to attach a record more than once. This is because you must also set up a primary `id` column on the pivot table for this feature to work.

Please ensure that the `id` attribute is listed in the `withPivot()` method of the relationship *and* inverse relationship.

Finally, add the `$allowsDuplicates` property to the relation manager:

```php
protected bool $allowsDuplicates = true;
```

## Associating and dissociating records

Filament is able to associate and dissociate records for `HasMany` and `MorphMany` relationships.

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
            Tables\Actions\BulkActionGroup::make([
                // ...
                Tables\Actions\DissociateBulkAction::make(),
            ]),
        ]);
}
```

### Preloading the associate modal select options

By default, as you search for a record to associate, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `preloadRecordSelect()` method of `AssociateAction`:

```php
use Filament\Tables\Actions\AssociateAction;

AssociateAction::make()
    ->preloadRecordSelect()
```

### Scoping the options to associate

You may want to scope the options available to `AssociateAction`:

```php
use Filament\Tables\Actions\AssociateAction;
use Illuminate\Database\Eloquent\Builder;

AssociateAction::make()
    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(auth()->user())
```

### Searching the options to associate across multiple columns

By default, the options available to `AssociateAction` will be searched in the `recordTitleAttribute()` of the table. If you wish to search across multiple columns, you can use the `recordSelectSearchColumns()` method:

```php
use Filament\Tables\Actions\AssociateAction;

AssociateAction::make()
    ->recordSelectSearchColumns(['title', 'description'])
```

### Customizing the select field in the associate modal

You may customize the select field object that is used during association by passing a function to the `recordSelect()` method:

```php
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AssociateAction;

AssociateAction::make()
    ->recordSelect(
        fn (Select $select) => $select->placeholder('Select a post'),
    )
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
        ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]))
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
            BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                // ...
            ]),
        ]);
}
```

### Customizing the `DeleteAction`

To learn how to customize the `DeleteAction`, including changing the notification and adding lifecycle hooks, please see the [Actions documentation](../../actions/prebuilt-actions/delete).

## Accessing the relationship's owner record

Relation managers are Livewire components. When they are first loaded, the owner record (the Eloquent record which serves as a parent - the main resource model) is saved into a property. You can read this property using:

```php
$this->getOwnerRecord()
```

However, if you're inside a `static` method like `form()` or `table()`, `$this` isn't accessible. So, you may [use a callback](../../forms/advanced#form-component-utility-injection) to access the `$livewire` instance:

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
                    return $livewire->getOwnerRecord()->stores()
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

You may use the `canViewForRecord()` method to determine if the relation manager should be visible for a specific owner record and page:

```php
use Illuminate\Database\Eloquent\Model;

public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
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

## Sharing a resource's form and table with a relation manager

You may decide that you want a resource's form and table to be identical to a relation manager's, and subsequently want to reuse the code you previously wrote. This is easy, by calling the `form()` and `table()` methods of the resource from the relation manager:

```php
use App\Filament\Resources\Blog\PostResource;
use Filament\Forms\Form;
use Filament\Tables\Table;

public function form(Form $form): Form
{
    return PostResource::form($form);
}

public function table(Table $table): Table
{
    return PostResource::table($table);
}
```

### Hiding a shared form component on the relation manager

If you're sharing a form component from the resource with the relation manager, you may want to hide it on the relation manager. This is especially useful if you want to hide a `Select` field for the owner record in the relation manager, since Filament will handle this for you anyway. To do this, you may use the `hiddenOn()` method, passing the name of the relation manager:

```php
use App\Filament\Resources\Blog\PostResource\RelationManagers\CommentsRelationManager;
use Filament\Forms\Components\Select;

Select::make('post_id')
    ->relationship('post', 'title')
    ->hiddenOn(CommentsRelationManager::class)
```

### Hiding a shared table column on the relation manager

If you're sharing a table column from the resource with the relation manager, you may want to hide it on the relation manager. This is especially useful if you want to hide a column for the owner record in the relation manager, since this is not appropriate when the owner record is already listed above the relation manager. To do this, you may use the `hiddenOn()` method, passing the name of the relation manager:

```php
use App\Filament\Resources\Blog\PostResource\RelationManagers\CommentsRelationManager;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('post.title')
    ->hiddenOn(CommentsRelationManager::class)
```

### Hiding a shared table filter on the relation manager

If you're sharing a table filter from the resource with the relation manager, you may want to hide it on the relation manager. This is especially useful if you want to hide a filter for the owner record in the relation manager, since this is not appropriate when the table is already filtered by the owner record. To do this, you may use the `hiddenOn()` method, passing the name of the relation manager:

```php
use App\Filament\Resources\Blog\PostResource\RelationManagers\CommentsRelationManager;
use Filament\Tables\Filters\SelectFilter;

SelectFilter::make('post')
    ->relationship('post', 'title')
    ->hiddenOn(CommentsRelationManager::class)
```

### Overriding shared configuration on the relation manager

Any configuration that you make inside the resource can be overwritten on the relation manager. For example, if you wanted to disable pagination on the relation manager's inherited table but not the resource itself:

```php
use App\Filament\Resources\Blog\PostResource;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return PostResource::table($table)
        ->paginated(false);
}
```

It is probably also useful to provide extra configuration on the relation manager if you wanted to add a header action to [create](#creating-related-records), [attach](#attaching-and-detaching-records), or [associate](#associating-and-dissociating-records) records in the relation manager:

```php
use App\Filament\Resources\Blog\PostResource;
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return PostResource::table($table)
        ->headerActions([
            Tables\Actions\CreateAction::make(),
            Tables\Actions\AttachAction::make(),
        ]);
}
```

## Customizing the relation manager Eloquent query

You can apply your own query constraints or [model scopes](https://laravel.com/docs/eloquent#query-scopes) that affect the entire relation manager. To do this, you can pass a function to the `modifyQueryUsing()` method of the table, inside which you can customize the query:

```php
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
        ->columns([
            // ...
        ]);
}
```

## Customizing the relation manager record title

The relation manager uses the concept of a "record title attribute" to determine which attribute of the related model should be used to identify it. When creating a relation manager, this attribute is passed as the third argument to the `make:filament-relation-manager` command:

```bash
php artisan make:filament-relation-manager CategoryResource posts title
```

In this example, the `title` attribute of the `Post` model will be used to identify a post in the relation manager.

This is mainly used by the action classes. For instance, when you [attach](#attaching-and-detaching-records) or [associate](#associating-and-dissociating-records) a record, the titles will be listed in the select field. When you [edit](#editing-related-records), [view](#viewing-related-records) or [delete](#deleting-related-records) a record, the title will be used in the header of the modal.

In some cases, you may want to concatenate multiple attributes together to form a title. You can do this by replacing the `recordTitleAttribute()` configuration method with `recordTitle()`, passing a function that transforms a model into a title:

```php
use App\Models\Post;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->recordTitle(fn (Post $record): string => "{$record->title} ({$record->id})")
        ->columns([
            // ...
        ]);
}
```

If you're using `recordTitle()`, and you have an [associate action](#associating-and-dissociating-records) or [attach action](#attaching-and-detaching-records), you will also want to specify search columns for those actions:

```php
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\AttachAction;

AssociateAction::make()
    ->recordSelectSearchColumns(['title', 'id']);

AttachAction::make()
    ->recordSelectSearchColumns(['title', 'id'])
```

## Passing properties to relation managers

When registering a relation manager in a resource, you can use the `make()` method to pass an array of [Livewire properties](https://livewire.laravel.com/docs/properties) to it:

```php
use App\Filament\Resources\Blog\PostResource\RelationManagers\CommentsRelationManager;

public static function getRelations(): array
{
    return [
        CommentsRelationManager::make([
            'status' => 'approved',
        ]),
    ];
}
```

This array of properties gets mapped to [public Livewire properties](https://livewire.laravel.com/docs/properties) on the relation manager class:

```php
use Filament\Resources\RelationManagers\RelationManager;

class CommentsRelationManager extends RelationManager
{
    public string $status;

    // ...
}
```

Now, you can access the `status` in the relation manager class using `$this->status`.
