---
title: Relation managers
---

"Relation managers" in Filament allow administrators to list, create, attach, associate, edit, detach, dissociate and delete related many records without leaving the resource's edit page. Resource classes contain a static `getRelations()` method that is used to register relation managers for your resource.

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

Once a table and form have been defined for the relation manager, visit the edit page of your resource to see it in action.

## Pivot attributes

Relation managers may also be used to edit pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the relation manager table and form, you can use:

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

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('role'),
            // ...
        ]);
}
```

Please ensure that any pivot attributes are listed in the `withPivot()` method of the relationship *and* inverse relationship.

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

## Associating and dissociating records

Filament is able to associate and dissociate records for the inverse `BelongsTo` relationship. To enable this functionality, you may add the following properties to the relation manager:

```php
protected static bool $hasAssociateAction = true;
protected static bool $hasDissociateAction = true;
protected static bool $hasDissociateBulkAction = true;
```

Each property represents a different action that you are able to enable for the relation manager:

- `$hasAssociateAction` enables an "Associate" button in the header of the table.
- `$hasDissociateAction` enables a "Dissociate" button on each row of the table.
- `$hasDissociateBulkAction` enables a "Dissociate" bulk action, which is available when you select one or more records.

## Handling duplicates

By default, you will not be allowed to attach a record more than once. This is because you must also set up a primary `id` column on the pivot table for this feature to work.

Please ensure that the `id` attribute is listed in the `withPivot()` method of the relationship *and* inverse relationship.

Finally, add the `$allowsDuplicates` property to the relation manager:

```php
protected bool $allowsDuplicates = true;
```
