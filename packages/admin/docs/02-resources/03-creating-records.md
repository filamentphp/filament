---
title: Creating records
---

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may define a `mutateFormDataBeforeCreate()` method, which accepts the `$data` as an array, and returns the modified version:

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

## Customizing the creation process

You can tweak how the record is created using the `handleRecordCreation()` method:

```php
use Illuminate\Database\Eloquent\Model;

protected function handleRecordCreation(array $data): Model
{
    return static::getModel()::create($data);
}
```

## Customizing form redirects

By default, after saving the form, the user will be redirected to the [Edit page](editing-records) of the resource, or the [View page](viewing-records) if it is present.

You may set up a custom redirect when the form is saved by overriding the `getRedirectUrl()` method.

For example, the form can redirect back to the [List page](listing-records):

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

## Customizing the save notification

When the record is successfully created, a notification is dispatched to the user, which indicates the success of their action.

To customize the text content of this notification:

```php
protected function getCreatedNotificationMessage(): ?string
{
    return 'User registered';
}
```

And to disable the notification altogether:

```php
protected function getCreatedNotificationMessage(): ?string
{
    return null;
}
```

## Lifecycle hooks

Hooks may be used to execute code at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the page class with the name of the hook:

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

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the Create page if the `create()` method of the model policy returns `true`.

## Wizards

You may easily transform the creation process into a multistep wizards.

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

Inside the `getSteps()` array, return your [wizard steps](../../forms/layout#wizard):

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
                    ->reactive()
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

Now, visit the Create page to see your wizard in action! The Edit page will still use the form defined within the resource class.

### Sharing fields between the resource form and wizards

If you'd like to reduce the amount of repetition between the resource form and wizard steps, it's a good idea to extract public static resource functions for your fields, where you can easily retrieve an instance of a field from the resource or the wizard:

```php
use Filament\Forms;
use Filament\Resources\Form;
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
            ->reactive()
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

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.create-user';
```
