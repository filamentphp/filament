---
title: Editing records
---

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may define a `mutateFormDataBeforeFill()` method on the Edit page class to modify the `$data` array, and return the modified version before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#customizing-data-before-filling-the-form).

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may define a `mutateFormDataBeforeSave()` method on the Edit page class, which accepts the `$data` as an array, and returns it modified:

```php
protected function mutateFormDataBeforeSave(array $data): array
{
    $data['last_edited_by_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#customizing-data-before-saving).

## Customizing the saving process

You can tweak how the record is updated using the `handleRecordUpdate()` method on the Edit page class:

```php
use Illuminate\Database\Eloquent\Model;

protected function handleRecordUpdate(Model $record, array $data): Model
{
    $record->update($data);

    return $record;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#customizing-the-saving-process).

## Customizing redirects

By default, saving the form will not redirect the user to another page.

You may set up a custom redirect when the form is saved by overriding the `getRedirectUrl()` method on the Edit page class.

For example, the form can redirect back to the [List page](listing-records) of the resource:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

Or the [View page](viewing-records):

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
}
```

If you wish to be redirected to the previous page, else the index page:

```php
protected function getRedirectUrl(): string
{
    return $this->previousUrl ?? $this->getResource()::getUrl('index');
}
```

## Customizing the save notification

When the record is successfully updated, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, define a `getSavedNotificationTitle()` method on the edit page class:

```php
protected function getSavedNotificationTitle(): ?string
{
    return 'User updated';
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#customizing-the-save-notification).

You may customize the entire notification by overriding the `getSavedNotification()` method on the edit page class:

```php
use Filament\Notifications\Notification;

protected function getSavedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('User updated')
        ->body('The user has been saved successfully.');
}
```

To disable the notification altogether, return `null` from the `getSavedNotification()` method on the edit page class:

```php
use Filament\Notifications\Notification;

protected function getSavedNotification(): ?Notification
{
    return null;
}
```

## Lifecycle hooks

Hooks may be used to execute code at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the Edit page class with the name of the hook:

```php
protected function beforeSave(): void
{
    // ...
}
```

In this example, the code in the `beforeSave()` method will be called before the data in the form is saved to the database.

There are several available hooks for the Edit pages:

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
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#lifecycle-hooks).

## Saving a part of the form independently

You may want to allow the user to save a part of the form independently of the rest of the form. One way to do this is with a [section action in the header or footer](../../forms/layout/section#adding-actions-to-the-sections-header-or-footer). From the `action()` method, you can call `saveFormComponentOnly()`, passing in the `Section` component that you want to save:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

Section::make('Rate limiting')
    ->schema([
        // ...
    ])
    ->footerActions([
        fn (string $operation): Action => Action::make('save')
            ->action(function (Section $component, EditRecord $livewire) {
                $livewire->saveFormComponentOnly($component);
                
                Notification::make()
                    ->title('Rate limiting saved')
                    ->body('The rate limiting settings have been saved successfully.')
                    ->success()
                    ->send();
            })
            ->visible($operation === 'edit'),
    ])
```

The `$operation` helper is available, to ensure that the action is only visible when the form is being edited.

## Halting the saving process

At any time, you may call `$this->halt()` from inside a lifecycle hook or mutation method, which will halt the entire saving process:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

protected function beforeSave(): void
{
    if (! $this->getRecord()->team->subscribed()) {
        Notification::make()
            ->warning()
            ->title('You don\'t have an active subscription!')
            ->body('Choose a plan to continue.')
            ->persistent()
            ->actions([
                Action::make('subscribe')
                    ->button()
                    ->url(route('subscribe'), shouldOpenInNewTab: true),
            ])
            ->send();

        $this->halt();
    }
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/edit#halting-the-saving-process).

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the Edit page if the `update()` method of the model policy returns `true`.

They also have the ability to delete the record if the `delete()` method of the policy returns `true`.

## Custom actions

"Actions" are buttons that are displayed on pages, which allow the user to run a Livewire method on the page or visit a URL.

On resource pages, actions are usually in 2 places: in the top right of the page, and below the form.

For example, you may add a new button action next to "Delete" on the Edit page:

```php
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('impersonate')
                ->action(function (): void {
                    // ...
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
```

Or, a new button next to "Save" below the form:

```php
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('close')->action('saveAndClose'),
        ];
    }

    public function saveAndClose(): void
    {
        // ...
    }
}
```

To view the entire actions API, please visit the [pages section](../pages#adding-actions-to-pages).

### Adding a save action button to the header

The "Save" button can be moved to the header of the page by overriding the `getHeaderActions()` method and using `getSaveFormAction()`. You need to pass `formId()` to the action, to specify that the action should submit the form with the ID of `form`, which is the `<form>` ID used in the view of the page:

```php
protected function getHeaderActions(): array
{
    return [
        $this->getSaveFormAction()
            ->formId('form'),
    ];
}
```

You may remove all actions from the form by overriding the `getFormActions()` method to return an empty array:

```php
protected function getFormActions(): array
{
    return [];
}
```

## Creating another Edit page

One Edit page may not be enough space to allow users to navigate many form fields. You can create as many Edit pages for a resource as you want. This is especially useful if you are using [resource sub-navigation](getting-started#resource-sub-navigation), as you are then easily able to switch between the different Edit pages.

To create an Edit page, you should use the `make:filament-page` command:

```bash
php artisan make:filament-page EditCustomerContact --resource=CustomerResource --type=EditRecord
```

You must register this new page in your resource's `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListCustomers::route('/'),
        'create' => Pages\CreateCustomer::route('/create'),
        'view' => Pages\ViewCustomer::route('/{record}'),
        'edit' => Pages\EditCustomer::route('/{record}/edit'),
        'edit-contact' => Pages\EditCustomerContact::route('/{record}/edit/contact'),
    ];
}
```

Now, you can define the `form()` for this page, which can contain other fields that are not present on the main Edit page:

```php
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
        ]);
}
```

## Adding edit pages to resource sub-navigation

If you're using [resource sub-navigation](getting-started#resource-sub-navigation), you can register this page as normal in `getRecordSubNavigation()` of the resource:

```php
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Pages\Page;

public static function getRecordSubNavigation(Page $page): array
{
    return $page->generateNavigationItems([
        // ...
        Pages\EditCustomerContact::class,
    ]);
}
```

## Custom views

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.edit-user';
```

This assumes that you have created a view at `resources/views/filament/resources/users/pages/edit-user.blade.php`.

Here's a basic example of what that view might contain:

```blade
<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$this->activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>
```

To see everything that the default view contains, you can check the `vendor/filament/filament/resources/views/resources/pages/edit-record.blade.php` file in your project.
