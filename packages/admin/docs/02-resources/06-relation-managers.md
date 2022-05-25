---
title: Relation managers
---

## Getting started

"Relation managers" in Filament allow administrators to list, create, attach, associate, edit, detach, dissociate and delete related many records without leaving the resource's Edit page. Resource classes contain a static `getRelations()` method that is used to register relation managers for your resource.

To create a relation manager, you can use one of the following commands, depending on the relationship type:

```bash
php artisan make:filament-has-many CategoryResource posts title
php artisan make:filament-has-many-through ProjectResource deployments title
php artisan make:filament-belongs-to-many UserResource teams name
php artisan make:filament-morph-many PostResource replies title
php artisan make:filament-morph-to-many TagResource posts title
```

- `CategoryResource` is the name of the resource class for the parent model.
- `posts` is the name of the relationship you want to manage.
- `title` is the name of the attribute that will be used to identify posts.

This will create a `CategoryResource/RelationManagers/PostsRelationManager.php` file. This contains a class where you are able to define a [form](getting-started#forms) and [table](getting-started#tables) for your relation manager:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ]);
}

public static function table(Table $table): Table
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

For relationships with unconventional naming conventions, you may wish to include the `$inverseRelationship` property on the relation manager:

```php
protected static ?string $inverseRelationship = 'section'; // Since the inverse related model is `Category`, this is normally `category`, not `section`.
```

Once a table and form have been defined for the relation manager, visit the [Edit](editing-records) or [View](viewing-records) page of your resource to see it in action.

## Listing records

Related records will be listed in a table. The entire relation manager is based around this table, which contains actions to [create](#creating-records), [edit](#editing-records), [attach / detach](#attaching-and-detaching-records), [associate / dissociate](#associating-and-dissociating-records), and delete records.

As per the documentation on [listing records](listing-records), you may use all the same utilities for customisation, but on the relation manager class instead of the List page class:

- [Columns](listing-records#columns)
- [Filters](listing-records#filters)
- [Actions](listing-records#actions)
- [Bulk actions](listing-records#bulk-actions)

Additionally, you may use any other feature of the [table builder](../../tables).

### Listing with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also add pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the table, you can use:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('role'),
        ]);
}
```

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

## Creating records

As per the documentation on [creating records](creating-records), you may use all the same utilities for customisation, but on the relation manager class instead of the Create page class:

- [Customizing data before saving](creating-records#customizing-data-before-saving)
- [Customizing the creation process](creating-records#customizing-the-creation-process)
- [Customizing the save notification](creating-records#customizing-the-save-notification)
- [Lifecycle hooks](creating-records#lifecycle-hooks), with the addition of specific `beforeCreateFill()`, `afterCreateFill()`, `beforeCreateValidate()` and `afterCreateValidate()`

### Creating with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also add pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the create form, you can use:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
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

## Editing records

As per the documentation on [editing records](editing-records), you may use all the same utilities for customisation, but on the relation manager instead of the Edit page class:

- [Customizing data before filling the form](editing-records#customizing-data-before-filling-the-form)
- [Customizing data before saving](editing-records#customizing-data-before-saving)
- [Customizing the saving process](editing-records#customizing-the-update-process)
- [Customizing the save notification](editing-records#customizing-the-save-notification)
- [Lifecycle hooks](editing-records#lifecycle-hooks), with the addition of specific `beforeEditFill()`, `afterEditFill()`, `beforeEditValidate()` and `afterEditValidate()`

### Editing with pivot attributes

For `BelongsToMany` and `MorphToMany` relationships, you may also edit pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the edit form, you can use:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
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

## Viewing records

Filament is able to show information about a record in a modal. To enable this functionality, you may add the `$hasViewAction` property to the relation manager:

```php
protected static bool $hasViewAction = true;
```

## Attaching and detaching records

Filament is able to attach and detach records for `BelongsToMany` and `MorphToMany` relationships.

### Preloading the attachment modal select options

By default, as you search for a record to attach, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `$shouldPreloadAttachFormRecordSelectOptions` property:

```php
protected static bool $shouldPreloadAttachFormRecordSelectOptions = true;
```

### Attaching with pivot attributes

When you attach record with the `Attach` button, you may wish to define a custom form to add pivot attributes to the relationship:

```php
use Filament\Forms;
use Filament\Resources\Form;

public static function attachForm(Form $form): Form
{
    return $form
        ->schema([
            static::getAttachFormRecordSelect(),
            Forms\Components\TextInput::make('role')->required(),
            // ...
        ]);
}
```

As included in the above example, you may use `getAttachFormRecordSelect()` to create a select field for the record to attach.

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

### Handling duplicates

By default, you will not be allowed to attach a record more than once. This is because you must also set up a primary `id` column on the pivot table for this feature to work.

Please ensure that the `id` attribute is listed in the `withPivot()` method of the relationship *and* inverse relationship.

Finally, add the `$allowsDuplicates` property to the relation manager:

```php
protected bool $allowsDuplicates = true;
```

## Associating and dissociating records

Filament is able to associate and dissociate records for `HasMany`, `HasManyThrough` and `MorphMany` relationships. To enable this functionality, you may add the following properties to the relation manager:

```php
protected static bool $hasAssociateAction = true;
protected static bool $hasDissociateAction = true;
protected static bool $hasDissociateBulkAction = true;
```

Each property represents a different action that you are able to enable for the relation manager:

- `$hasAssociateAction` enables an "Associate" button in the header of the table.
- `$hasDissociateAction` enables a "Dissociate" button on each row of the table.
- `$hasDissociateBulkAction` enables a "Dissociate" bulk action, which is available when you select one or more records.

### Preloading the associate modal select options

By default, as you search for a record to associate, options will load from the database via AJAX. If you wish to preload these options when the form is first loaded instead, you can use the `$shouldPreloadAssociateFormRecordSelectOptions` property:

```php
protected static bool $shouldPreloadAssociateFormRecordSelectOptions = true;
```

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
