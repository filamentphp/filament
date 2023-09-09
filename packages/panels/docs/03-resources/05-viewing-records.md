---
title: Viewing records
---

## Creating a resource with a View page

To create a new resource with a View page, you can use the `--view` flag:

```bash
php artisan make:filament-resource User --view
```

## Using an infolist instead of a disabled form

By default, the View page will display a disabled form with the record's data. If you preferred to display the record's data in an "infolist", you can use define an `infolist()` method on the resource class:

```php
use Filament\Infolists;
use Filament\Infolists\Infolist;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\TextEntry::make('name'),
            Infolists\Components\TextEntry::make('email'),
            Infolists\Components\TextEntry::make('notes')
                ->columnSpanFull(),
        ]);
}
```

The `schema()` method is used to define the structure of your infolist. It is an array of [entries](../../infolists/entries/getting-started#available-entries) and [layout components](../../infolists/layout/getting-started#available-layout-components), in the order they should appear in your infolist.

Check out the Infolists docs for a [guide](../../infolists/getting-started) on how to build infolists with Filament.

## Adding a View page to an existing resource

If you want to add a View page to an existing resource, create a new page in your resource's `Pages` directory:

```bash
php artisan make:filament-page ViewUser --resource=UserResource --type=ViewRecord
```

You must register this new page in your resource's `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListUsers::route('/'),
        'create' => Pages\CreateUser::route('/create'),
        'view' => Pages\ViewUser::route('/{record}'),
        'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
}
```

## Viewing records in modals

If your resource is simple, you may wish to view records in modals rather than on the [View page](viewing-records). If this is the case, you can just [delete the view page](getting-started#deleting-resource-pages).

If your resource doesn't contain a `ViewAction`, you can add one to the `$table->actions()` array:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
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

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may define a `mutateFormDataBeforeFill()` method on the View page class to modify the `$data` array, and return the modified version before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're viewing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/view#customizing-data-before-filling-the-form).

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the View page if the `view()` method of the model policy returns `true`.

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.view-user';
```

This assumes that you have created a view at `resources/views/filament/resources/users/pages/view-user.blade.php`.

Here's a basic example of what that view might contain:

```blade
<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        {{ $this->form }}
    @endif

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>
```

To see everything that the default view contains, you can check the `vendor/filament/filament/resources/views/resources/pages/view-record.blade.php` file in your project.
