---
title: Creating records
---

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may define a `mutateFormDataBeforeCreate()` method on the Create page class, which accepts the `$data` as an array, and returns the modified version:

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#customizing-data-before-saving).

## Customizing the creation process

You can tweak how the record is created using the `handleRecordCreation()` method on the Create page class:

```php
use Illuminate\Database\Eloquent\Model;

protected function handleRecordCreation(array $data): Model
{
    return static::getModel()::create($data);
}
```

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#customizing-the-creation-process).

## Customizing redirects

By default, after saving the form, the user will be redirected to the [Edit page](editing-records) of the resource, or the [View page](viewing-records) if it is present.

You may set up a custom redirect when the form is saved by overriding the `getRedirectUrl()` method on the Create page class.

For example, the form can redirect back to the [List page](listing-records):

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
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

When the record is successfully created, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, define a `getCreatedNotificationTitle()` method on the create page class:

```php
protected function getCreatedNotificationTitle(): ?string
{
    return 'User registered';
}
```

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#customizing-the-save-notification).

You may customize the entire notification by overriding the `getCreatedNotification()` method on the create page class:

```php
use Filament\Notifications\Notification;

protected function getCreatedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('User registered')
        ->body('The user has been created successfully.');
}
```

To disable the notification altogether, return `null` from the `getCreatedNotification()` method on the create page class:

```php
use Filament\Notifications\Notification;

protected function getCreatedNotification(): ?Notification
{
    return null;
}
```

## Lifecycle hooks

Hooks may be used to execute code at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the Create page class with the name of the hook:

```php
protected function beforeCreate(): void
{
    // ...
}
```

In this example, the code in the `beforeCreate()` method will be called before the data in the form is saved to the database.

There are several available hooks for the Create page:

```php
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    // ...

    protected function beforeFill(): void
    {
        // Runs before the form fields are populated with their default values.
    }

    protected function afterFill(): void
    {
        // Runs after the form fields are populated with their default values.
    }

    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is submitted.
    }

    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is submitted.
    }

    protected function beforeCreate(): void
    {
        // Runs before the form fields are saved to the database.
    }

    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
    }
}
```

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#lifecycle-hooks).

## Halting the creation process

At any time, you may call `$this->halt()` from inside a lifecycle hook or mutation method, which will halt the entire creation process:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

protected function beforeCreate(): void
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

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#halting-the-creation-process).

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the Create page if the `create()` method of the model policy returns `true`.

## Using a wizard

You may easily transform the creation process into a multistep wizard.

On the page class, add the corresponding `HasWizard` trait:

```php
use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    
    protected static string $resource = CategoryResource::class;

    protected function getSteps(): array
    {
        return [
            // ...
        ];
    }
}
```

Inside the `getSteps()` array, return your [wizard steps](../../forms/layout/wizard):

```php
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;

protected function getSteps(): array
{
    return [
        Step::make('Name')
            ->description('Give the category a clear and unique name')
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live()
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
    ];
}
```

Alternatively, if you're creating records in a modal action, check out the [Actions documentation](../../actions/prebuilt-actions/create#using-a-wizard).

Now, create a new record to see your wizard in action! Edit will still use the form defined within the resource class.

If you'd like to allow free navigation, so all the steps are skippable, override the `hasSkippableSteps()` method:

```php
public function hasSkippableSteps(): bool
{
    return true;
}
```

### Sharing fields between the resource form and wizards

If you'd like to reduce the amount of repetition between the resource form and wizard steps, it's a good idea to extract public static resource functions for your fields, where you can easily retrieve an instance of a field from the resource or the wizard:

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class CategoryResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::getNameFormField(),
                static::getSlugFormField(),
                // ...
            ]);
    }
    
    public static function getNameFormField(): Forms\Components\TextInput
    {
        return TextInput::make('name')
            ->required()
            ->live()
            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)));
    }
    
    public static function getSlugFormField(): Forms\Components\TextInput
    {
        return TextInput::make('slug')
            ->disabled()
            ->required()
            ->unique(Category::class, 'slug', fn ($record) => $record);
    }
}
```

```php
use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    
    protected static string $resource = CategoryResource::class;

    protected function getSteps(): array
    {
        return [
            Step::make('Name')
                ->description('Give the category a clear and unique name')
                ->schema([
                    CategoryResource::getNameFormField(),
                    CategoryResource::getSlugFormField(),
                ]),
            // ...
        ];
    }
}
```

## Importing resource records

Filament includes an `ImportAction` that you can add to the `getHeaderActions()` of the [List page](listing-records). It allows users to upload a CSV of data to import into the resource:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions;

protected function getHeaderActions(): array
{
    return [
        Actions\ImportAction::make()
            ->importer(ProductImporter::class),
        Actions\CreateAction::make(),
    ];
}
```

The "importer" class [needs to be created](../../actions/prebuilt-actions/import#creating-an-importer) to tell Filament how to import each row of the CSV. You can learn everything about the `ImportAction` in the [Actions documentation](../../actions/prebuilt-actions/import).

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.create-user';
```

This assumes that you have created a view at `resources/views/filament/resources/users/pages/create-user.blade.php`.

Here's a basic example of what that view might contain:

```blade
<x-filament-panels::page>
    <x-filament-panels::form wire:submit="create">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
```

To see everything that the default view contains, you can check the `vendor/filament/filament/resources/views/resources/pages/create-record.blade.php` file in your project.
