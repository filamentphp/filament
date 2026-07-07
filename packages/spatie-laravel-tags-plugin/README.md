# Filament Spatie Tags Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-tags-plugin:"^4.0" -W
```

If you haven't already done so, you need to publish the migration to create the tags table:

```bash
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

You must also [prepare your Eloquent model](https://spatie.be/docs/laravel-tags/basic-usage/using-tags) for attaching tags.

> For more information, check out [Spatie's documentation](https://spatie.be/docs/laravel-tags).

## Form component

This guide assumes that you've already set up your model to attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

You may use the field in the same way as the [original tags input](https://filamentphp.com/docs/forms/tags-input) field:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
```

Optionally, you may pass a [`type()`](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) that allows you to group tags into collections:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
    ->type('categories')
```

The tags input supports all the customization options of the [original tags input component](https://filamentphp.com/docs/forms/tags-input).

### Security: tag types and privilege namespaces

When you do not pass `->type(...)` to a `SpatieTagsInput`, the field matches and attaches tags across **every** type in your database. Internally, it calls `Tag::findFromStringOfAnyType($name)`, which returns the first existing tag matching by name — regardless of its `type`. This is convenient for general-purpose tagging, but it has a security implication if you also use tag types as a **privilege namespace**.

For example, suppose your application stores roles as tags:

```php
$user->attachTag('admin', type: 'role');

// Later, in an authorisation check:
if ($user->hasTag('admin', 'role')) {
    // ...
}
```

If you then render a tags input on the same model without specifying a type — for example, a "Hobbies" field on the user's own profile — a low-privilege user can submit `admin` into that field. The save callback finds the existing `role:admin` tag (because it matches the name across all types), attaches it to the user, and the authorisation check now returns `true`. Autocomplete suggestions from `getSuggestions()` will also list every tag name in the database when no type is set, so the privileged tag names are visible to the attacker.

To prevent this, do at least one of the following:

- **Always pass `->type(...)` on user-facing tag inputs.** Restricting the input to a specific type means the save callback uses `syncTagsWithType()` instead of `syncTagsWithAnyType()`, so tags of other types can never be attached:

    ```php
    SpatieTagsInput::make('hobbies')
        ->type('hobbies')
    ```

    If you have no need for tag types at all but want the safe scoping, pass `->type(null)`. The field will then read and write only untyped tags (`type IS NULL`):

    ```php
    SpatieTagsInput::make('tags')
        ->type(null)
    ```

- **Do not use Spatie tag types as a privilege namespace** if you also expose a user-editable `SpatieTagsInput` on the same model. Store roles and permissions in a dedicated table (e.g. `spatie/laravel-permission`), or in a separate, never-user-editable model relationship.

- **Authorise the field itself** with `->disabled()` or `->hidden()` when the user lacks permission to manage tags on the record, paired with a server-side authorisation check.

The same caveat applies to `SpatieTagsColumn` (editable inline) — pass `->type(...)` when the column should be scoped to a single tag type.

## Table column

This guide assumes that you've already set up your model attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

To use the tags column:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags')
```

Optionally, you may pass a `type()`:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags')
    ->type('categories')
```

The [type](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) allows you to group tags into collections.

The tags column supports all the customization options of the [original tags column](https://filamentphp.com/docs/tables/columns/tags).

## Infolist entry

This guide assumes that you've already set up your model attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

To use the tags entry:

```php
use Filament\Infolists\Components\SpatieTagsEntry;

SpatieTagsEntry::make('tags')
```

Optionally, you may pass a `type()`:

```php
use Filament\Infolists\Components\SpatieTagsEntry;

SpatieTagsEntry::make('tags')
    ->type('categories')
```

The [type](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) allows you to group tags into collections.

The tags entry supports all the customization options of the [text entry](https://filamentphp.com/docs/infolists/entries/text).

## Bulk actions

You may add bulk actions to a table, allowing your users to attach tags to all selected records, or detach tags from them:

```php
use Filament\Actions\AttachSpatieTagsBulkAction;
use Filament\Actions\DetachSpatieTagsBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            AttachSpatieTagsBulkAction::make(),
            DetachSpatieTagsBulkAction::make(),
        ]);
}
```

Each action opens a modal with a tags input, which suggests existing tags from the database. Attaching tags preserves any tags that the records already have, and creates any tags that do not exist yet. Detaching tags removes the chosen tags from the selected records, without deleting the tags themselves from the database.

### Managing tags with a single bulk action

Instead of separate attach and detach actions, you may use `ManageSpatieTagsBulkAction`, which opens a single modal with two tags inputs, allowing your users to attach some tags and detach others in a single pass over the selected records:

```php
use Filament\Actions\ManageSpatieTagsBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->toolbarActions([
            ManageSpatieTagsBulkAction::make(),
        ]);
}
```

At least one of the two fields must be filled in, and the same tag may not be attached and detached at the same time. Attaching and detaching behave identically to the standalone actions.

Optionally, you may pass a [`type()`](https://spatie.be/docs/laravel-tags/advanced-usage/using-types), which scopes the tags that the action can attach or detach:

```php
use Filament\Actions\AttachSpatieTagsBulkAction;

AttachSpatieTagsBulkAction::make()
    ->type('categories')
```

When no `type()` is passed, the actions match tags across every type, in the same way as the `SpatieTagsInput` field. The [security caveats around tag types and privilege namespaces](#security-tag-types-and-privilege-namespaces) apply equally to these bulk actions, so pass `->type(...)` (or `->type(null)` for untyped tags only) when tag types are used as a privilege namespace in your application.

You may authorize each selected record individually using [`authorizeIndividualRecords()`](https://filamentphp.com/docs/actions/create#authorization), passing the name of a policy method. Records that fail the authorization check are skipped, and the user is notified:

```php
use Filament\Actions\AttachSpatieTagsBulkAction;

AttachSpatieTagsBulkAction::make()
    ->authorizeIndividualRecords('update')
```

The bulk actions support all the customization options of [regular bulk actions](https://filamentphp.com/docs/actions/overview), such as `label()` and `successNotificationTitle()`.
