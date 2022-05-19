---
title: Forms
---

Resource classes contain a static `form()` method that is used to build the forms on the create and edit pages:

```php
use Filament\Forms;
use Filament\Resources\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            // ...
        ]);
}
```

## Fields

The `schema()` method is used to define the structure of your form. It is an array of [fields](../../forms/fields), in the order they should appear in your form.

We have many fields available for your forms, including:

- [Text input](../../forms/fields#text-input)
- [Select](../../forms/fields#select)
- [Multi-select](../../forms/fields#multi-select)
- [Checkbox](../../forms/fields#checkbox)
- [Date-time picker](../../forms/fields#date-time-picker)
- [File upload](../../forms/fields#file-upload)
- [Rich editor](../../forms/fields#rich-editor)
- [Markdown editor](../../forms/fields#markdown-editor)
- [Repeater](../../forms/fields#repeater)

To view a full list of available form [fields](../../forms/fields), see the [Form Builder documentation](../../forms/fields).

You may also build your own completely [custom form fields](../../forms/fields#building-custom-fields).

## Layout

Form layouts are completely customizable. We have many layout components available, which can be used in any combination:

- [Grid](../../forms/layout#grid)
- [Card](../../forms/layout#card)
- [Tabs](../../forms/layout#tabs)

To view a full list of available [layout components](../../forms/layout), see the [Form Builder documentation](../../forms/layout).

You may also build your own completely [custom layout components](../../forms/layout#building-custom-layout-components).

## Hiding components based on the page

The `hiddenOn()` method of form components allows you to dynamically hide fields based on the current page.

In this example, we hide the `password` field on the `EditUser` resource page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn(Pages\EditUser::class),
```

Alternatively, we have a `visibleOn()` shortcut method for only showing a field on one page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->visibleOn(Pages\CreateUser::class),
```

## Wizards

You may easily transform resource forms, such as the [Create page](creating-records), into multistep wizards.

On the [Create page class](creating-records), add the corresponding `HasWizard` trait:

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

Now, visit the [Create page](creating-records) to see your wizard in action! The edit page will still use the form defined within the resource class.

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
