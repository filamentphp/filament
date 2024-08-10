---
title: Overview
---

## What is a schema?

In Filament, a schema allows you to render components in a view. Components could be:

- [Form fields](../forms/fields), which accept input from the user. For example, a text input, a select, or a checkbox.
- [Infolist entries](../infolists/entries), which are small read-only displays of data. For example, a text entry, an icon entry, or an image entry.
- [Layout components](layout), which are used to structure the components. For example, a grid, tabs, or a wizard.

Schemas are like a container for components, and you can add any combination of components within it.

A schema is represented by a `Filament\Schema\Schema` object, and you can pass an array of components to it in the `schema()` method.

For example, you may want to build a form with a schema. The name of the schema is usually dictated by the name of the method that configures it (`form` in this example). Filament instantiates the `Schema` object, and passes it to the method, which then returns the schema with the components added:

```php
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schema\Schema;

public function form(Schema $form): Schema
{
    return $form
        ->schema([
            TextInput::make('name'),
            Select::make('position')
                ->options([
                    'developer' => 'Developer',
                    'designer' => 'Designer',
                ]),
            Checkbox::make('is_admin'),
            // More components can be added here
        ]);
}
```

The schema object is the container for the components, and can now be rendered. Rendering the schema will render all the components within it in the correct layout.

## What can you use a schema for in Filament?

You can use a schema to:

- Manage the content of a page in a [panel CRUD resource](../panels/resources). There are 3 main pages in a resource that use a schema:
  - The [Create page](../panels/resources/creating-records) uses a schema to render a form to create a record.
  - The [Edit page](../panels/resources/editing-records) uses a schema to render a form to edit a record.
  - The [View page](../panels/resources/viewing-records) page uses a schema to render a disabled form, or a read-only infolist, to view the details about a record.
- Render content in an [action modal](../actions/modals), such as a form or an infolist. Data from the form is submitted to the server when the action is run.
- Render content, such as a form, in a Livewire component. Livewire components can be added to any Blade view even if you haven't started your project using Livewire, so any project that uses Blade to render views can use Filament.
