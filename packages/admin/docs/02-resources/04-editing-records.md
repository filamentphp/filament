---
title: Editing Records
---

## Customizing data before filling the form

On edit pages, you may define a `mutateFormDataBeforeFill()` method to modify the record data before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

## Customizing data before saving

You may define a `mutateFormDataBeforeSave()` method to modify the form data before it is saved to the database:

```php
protected function mutateFormDataBeforeSave(array $data): array
{
    $data['last_edited_by_id'] = auth()->id();

    return $data;
}
```

## Customizing form redirects

You may set up a custom redirect when the form is saved by overriding the `getRedirectUrl()` method.

For example, the form can redirect back to the [View page](viewing-records) when it is submitted:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->record]);
}
```

## Lifecycle hooks

Hooks may be used to execute methods at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the page class with the name of the hook:

```php
protected function beforeSave(): void
{
    // ...
}
```

In this example, the code in the `beforeSave()` method will be called before the data in the form is saved to the database.

There are several available hooks for the edit pages:

```php
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function beforeFill(): void
    {
        // Runs before the form fields are populated from the database.
    }

    protected function afterFill(): void
    {
        // Runs after the form fields are populated from the database.
    }

    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is saved.
    }

    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is saved.
    }

    protected function beforeSave(): void
    {
        // Runs before the form fields are saved to the database.
    }

    protected function afterSave(): void
    {
        // Runs after the form fields are saved to the database.
    }

    protected function beforeDelete(): void
    {
        // Runs before the record is deleted.
    }

    protected function afterDelete(): void
    {
        // Runs after the record is deleted.
    }
}
```

## Custom actions

"Actions" are buttons that are displayed on pages, which allow the user to run a Livewire method on the page or visit a URL.

On resource pages, actions are usually in 2 places: in the top right of the page, and below the form.

For example, you may add a new button action next to "Delete" on the edit page that runs the `impersonate()` Livewire method:

```php
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getActions(): array
    {
        return array_merge(parent::getActions(), [
            Action::make('impersonate')->action('impersonate'),
        ]);
    }

    public function impersonate(): void
    {
        // ...
    }
}
```

Or, a new button next to "Save" below the form:

```php
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            Action::make('close')->action('saveAndClose'),
        ]);
    }

    public function saveAndClose(): void
    {
        // ...
    }
}
```

To view the entire actions API, please visit the [pages section](../pages/actions).

## Custom views

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.edit-user';
```
