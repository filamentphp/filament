---
title: Building Forms
description:
extends: _layouts.documentation
section: content
toc: |
  - [Fields](#fields)
      - [Checkbox](#fields-checkbox)
      - [Date Picker](#fields-date-picker)
      - [Date-time Picker](#fields-date-time-picker)
      - [File Upload](#fields-file-upload)
      - [Key-value](#fields-key-value)
      - [Markdown Editor](#fields-markdown-editor)
        - [Toolbar Buttons](#fields-markdown-editor-toolbar-buttons)
      - [Rich Editor](#fields-rich-editor)
          - [Toolbar Buttons](#fields-rich-editor-toolbar-buttons)
      - [Select](#fields-select)
      - [Tags Input](#fields-tags-input)
      - [Textarea](#fields-textarea)
      - [Text Input](#fields-text-input)
      - [Toggle](#fields-toggle)
  - [Validation](#validation)
  - [Layout](#layout)
      - [Grid](#layout-grid)
      - [Section](#layout-section)
      - [Fieldset](#layout-fieldset)
      - [Tabs](#layout-tabs)
      - [Group](#layout-group)
      - [Placeholder](#layout-placeholder)
  - [Dependent Fields](#dependent-fields)
  - [Context Customization](#context-customization)
  - [Developing Custom Components](#custom-development)
---

# Building Forms

<p class="lg:text-2xl">Filament comes with a powerful form builder which can be used to create intuitive, dynamic, and contextual forms in the admin panel.</p>

Forms have a schema, which is an array that contains many form components. The schema defines the form's [fields](#fields), their [validation rules](#validation), and their [layout](#layout) in the form.

Here is an example form configuration for a `CustomerResource`:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            Components\TextInput::make('name')->autofocus()->required(),
            Components\TextInput::make('email')->email()->required(),
            Components\Select::make('type')
                ->placeholder('Select a type')
                ->options([
                    'individual' => 'Individual',
                    'organization' => 'Organization',
                ]),
            Components\DatePicker::make('birthday'),
        ])
        ->columns(2);
}
```

> Please note: when building forms for resources, please ensure that you are using components within the `Filament\Resources\Forms\Components` namespace and not `Filament\Forms\Components`.

## Fields {#fields}

Resource field classes are located in the `Filament\Resources\Forms\Components` namespace.

All field components have access to the following customization methods:

```php
Field::make($name)
    ->columnSpan($span = 1) // On large devices, this sets the number of columns that the field should span in the form.
    ->default($default) // Sets the default value for this field.
    ->dependable() // Reloads the form when this field is changed.
    ->disabled($disabled = false) // Make the field read-only.
    ->extraAttributes($attributes = []) // A key-value array of extra HTML attributes to pass to the field.
    ->helpMessage($message) // Sets an optional message below the field. It supports Markdown.
    ->hint($hint) // Sets an optional short message adjacent to the label. It supports Markdown.
    ->id($id) // Set the HTML ID of the field, which is otherwise automatically generated based on its name.
    ->label($label); // Set custom label text for with the field, which is otherwise automatically generated based on its name. It supports localization strings.
```

### Checkbox {#fields-checkbox}

```php
Checkbox::make($name)
    ->autofocus() // Autofocus the field.
    ->inline() // Render the checkbox inline with its label.
    ->stacked(); // Render the checkbox under its label.
```

### Date Picker {#fields-date-picker}

```php
DatePicker::make($name)
    ->autofocus() // Autofocus the field.
    ->displayFormat($format = 'F j, Y') // Set the display format of the field, using PHP date formatting tokens.
    ->firstDayOfWeek($day = 1) // Set the first day of the week in the calendar view, with 1 being Monday, and 0 or 7 being Sunday.
    ->format($format = 'Y-m-d') // Set the storage format of the field, using PHP date formatting tokens.
    ->maxDate($date) // Set the maximum date that can be selected.
    ->minDate($date) // Set the minimum date that can be selected.
    ->placeholder($placeholder) // Set the placeholder for when the field is empty. It supports localization strings.
    ->weekStartsOnMonday() // Set the first day of the week to Monday in the calendar view.
    ->weekStartsOnSunday(); // Set the first day of the week to Sunday in the calendar view.
```

### Date-time Picker {#fields-date-time-picker}

```php
DateTimePicker::make($name)
    ->autofocus() // Autofocus the field.
    ->displayFormat($format = 'F j, Y H:i:s') // Set the display format of the field, using PHP date formatting tokens.
    ->firstDayOfWeek($day = 1) // Set the first day of the week in the calendar view, with 1 being Monday, and 0 or 7 being Sunday.
    ->format($format = 'Y-m-d H:i:s') // Set the storage format of the field, using PHP date formatting tokens.
    ->maxDate($date) // Set the maximum date that can be selected.
    ->minDate($date) // Set the minimum date that can be selected.
    ->placeholder($placeholder) // Set the placeholder for when the field is empty. It supports localization strings.
    ->weekStartsOnMonday() // Set the first day of the week to Monday in the calendar view.
    ->weekStartsOnSunday() // Set the first day of the week to Sunday in the calendar view.
    ->withoutSeconds(); // Hide the seconds input.
```

### File Upload {#fields-file-upload}

```php
FileUpload::make($name)
    ->acceptedFileTypes($types = []) // Limit the type of files that can be uploaded using an array of mime types.
    ->avatar() // Make the field suitable for uploading and displaying a circular avatar.
    ->disk($disk) // Set a custom disk that uploaded files should be read from and written to.
    ->directory($directory) // Set a custom directory that uploaded files should be written to.
    ->image() // Allow only images to be uploaded.
    ->imageCropAspectRatio($ratio) // Crop images to this certain aspect ratio when they are uploaded, e.g: '1:1'.
    ->imagePreviewHeight($height) // Set the height of the image preview in pixels.
    ->imageResizeTargetHeight($height) // Resize images to this height (in pixels) when they are uploaded.
    ->imageResizeTargetWidth($width) // Resize images to this width (in pixels) when they are uploaded.
    ->loadingIndicatorPosition($position = 'right') // Set the position of the loading indicator.
    ->maxSize($size) // Set the maximum size of files that can be uploaded, in kilobytes.
    ->minSize($size) // Set the minimum size of files that can be uploaded, in kilobytes.
    ->panelAspectRatio($ratio) // Set the aspect ratio of the panel, e.g: '1:1'.
    ->panelLayout($layout) // Set the layout of the panel.
    ->placeholder($placeholder) // Set the placeholder for when no file has been uploaded. It supports localization strings.
    ->removeUploadButtonPosition($position = 'left') // Set the position of the remove upload button.
    ->uploadButtonPosition($position = 'right') // Set the position of the upload button.
    ->uploadProgressIndicatorPosition($position = 'right') // Set the position of the upload progress indicator.
    ->visibility($visibility = 'public'); // Set the visibility of uploaded files.
```

> Please note, it is the responsibility of the developer to delete these files from the disk if they are removed, as Filament is unaware if they are depended on elsewhere. One way to do this automatically is observing a [model event](https://laravel.com/docs/eloquent#events).

> To customize Livewire's default file upload validation rules, please refer to its [documentation](https://laravel-livewire.com/docs/file-uploads#global-validation).

> Available values for the position methods can be found on [Filepond's website](https://pqina.nl/filepond/docs/patterns/api/filepond-instance#styles).

> Support for multiple file uploads is coming soon. For more information, please see our [Development Roadmap](/docs/roadmap).

### Key-value {#fields-key-value}

```php
KeyValue::make($name)
    ->addButtonLabel($label) // Set the add button label. It supports localization strings.
    ->deleteButtonLabel($label) // Set the delete button label. It supports localization strings.
    ->disableAddingRows($state = false) // Disable the addition of rows.
    ->disableDeletingRows($state = false) // Disable the deletion of rows.
    ->disableEditingKeys($state = false) // Disable the editing of keys.
    ->keyLabel($label) // Set the key field label label. It supports localization strings.
    ->keyPlaceholder($placeholder) // Set the key field placeholder. It supports localization strings.
    ->sortable($sortable = true) // Allow the keys to be sorted using drag and drop.
    ->sortButtonLabel($label) // Set the sort button label. It supports localization strings.
    ->valueLabel($label) // Set the value field label label. It supports localization strings.
    ->valuePlaceholder($placeholder); // Set the value field placeholder. It supports localization strings.
```

### Markdown Editor {#fields-markdown-editor}

```php
MarkdownEditor::make($name)
    ->attachmentDisk($disk) // Set a custom disk that uploaded attachments should be read from and written to.
    ->attachmentDirectory($directory) // Set a custom directory that uploaded attachments should be written to.
    ->autofocus() // Autofocus the field.
    ->disableAllToolbarButtons() // Disable all toolbar buttons.
    ->disableToolbarButtons($buttons = []) // Disable toolbar buttons. See below for options.
    ->enableToolbarButtons($buttons = []) // Enable toolbar buttons. See below for options.
    ->placeholder($placeholder); // Set the placeholder for when the field is empty. It supports localization strings.
```

#### Toolbar Buttons {#fields-markdown-editor-toolbar-buttons}

```
attachFiles
bold
bullet
code
italic
link
number
preview
strike
write
```

### Rich Editor {#fields-rich-editor}

```php
RichEditor::make($name)
    ->attachmentDisk($disk) // Set a custom disk that uploaded attachments should be read from and written to.
    ->attachmentDirectory($directory) // Set a custom directory that uploaded attachments should be written to.
    ->autofocus() // Autofocus the field.
    ->disableAllToolbarButtons() // Disable all toolbar buttons.
    ->disableToolbarButtons($buttons = []) // Disable toolbar buttons. See below for options.
    ->enableToolbarButtons($buttons = []) // Enable toolbar buttons. See below for options.
    ->placeholder($placeholder); // Set the placeholder for when the field is empty. It supports localization strings.
```

#### Toolbar Buttons {#fields-rich-editor-toolbar-buttons}

```
attachFiles
bold
bullet
code
heading
italic
link
number
quote
redo
strike
subheading
title
undo
```

### Select {#fields-select}

```php
Select::make($name)
    ->autofocus() // Autofocus the field.
    ->emptyOptionsMessage($message) // Set the message for when there are no options available to pick from. It supports localization strings.
    ->noSearchResultsMessage($message) // Set the message for when there are no option search results. It supports localization strings.
    ->options($options = []) // Set the key-value array of available options to pick from.
    ->placeholder($placeholder); // Set the placeholder for when the field is empty. It supports localization strings.
```

> If you're looking to use a select for a `belongsTo()` relationship, please check out the [`BelongsToSelect` resource field](/docs/resources#relations-single).

### Tags Input {#fields-tags-input}

```php
TagsInput::make($name)
    ->autofocus() // Autofocus the field.
    ->placeholder($placeholder) // Set the placeholder for when the new tag field is empty. It supports localization strings.
    ->separator($separator = ','); // Set the separator that should be used between tags.
```

### Textarea {#fields-textarea}

```php
Textarea::make($name)
    ->autocomplete($autocomplete = 'on') // Set up autocomplete for the field.
    ->autofocus() // Autofocus the field.
    ->cols($cols) // The number of columns wide the textarea is.
    ->disableAutocomplete() // Disable autocomplete for the field.
    ->placeholder($placeholder); // Set the placeholder for when the field is empty. It supports localization strings.
    ->rows($rows) // The number of rows tall the textarea is.
```

### Text Input {#fields-text-input}

```php
TextInput::make($name)
    ->autocomplete($autocomplete = 'on') // Set up autocomplete for the field.
    ->autofocus() // Autofocus the field.
    ->disableAutocomplete() // Disable autocomplete for the field.
    ->email() // Require a valid email address to be provided.
    ->max($max) // Set a maximum numeric value to be provided.
    ->min($min) // Set a minimum numeric value to be provided.
    ->numeric() // Require a numeric value to be provided.
    ->password() // Obfuscate the field's value.
    ->placeholder($placeholder) // Set the placeholder for when the field is empty. It supports localization strings.
    ->postfix($postfix) // Set a postfix label to be displayed after the input.
    ->prefix($prefix) // Set a prefix label to be displayed before the input.
    ->tel() // Require a valid telephone number to be provided.
    ->type($type = 'text') // Set the input's HTML type.
    ->url(); // Require a valid URL to be provided.
```

### Toggle {#fields-toggle}

The `onIcon()` and `offIcon()` methods support the name of any Blade icon component, and passes a set of formatting classes to it. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
Toggle::make($name)
    ->autofocus() // Autofocus the field.
    ->inline() // Render the toggle inline with its label.
    ->offIcon($icon) // Set the icon that should be displayed when the toggle is off.
    ->onIcon($icon) // Set the icon that should be displayed when the toggle is on.
    ->stacked(); // Render the toggle under its label.
```

## Validation {#validation}

Filament provides a number of validation methods that can be applied to fields. Please refer to the [Laravel Validation docs](https://laravel.com/docs/validation#available-validation-rules) if you are unsure about any of these.

```php
->acceptedFileTypes($types = []) // Accepts an array of mime types, file upload field only.
->confirmed($field = '{field name}Confirmation') // Text-based fields only.
->email() // Text input field only.
->image() // File upload field only.
->max($value) // Text input field only.
->maxDate($date) // Date-based fields only.
->maxLength($length) // Text-based fields only.
->maxSize($size) // In kilobytes, file upload field only.
->min($value) // Text input field only.
->minDate($date) // Date-based fields only.
->minLength($length) // Text-based fields only.
->minSize($size) // In kilobytes, file upload field only.
->nullable() // Applied to all fields by default.
->numeric() // Text input field only.
->required()
->requiredWith()
->same($field) // Text-based fields only.
->tel() // Text input field only.
->unique($table, $column = '{field name}', $exceptCurrentRecord = false)
->url() // Text input field only.
```

You may apply additional custom validation rules to any field using the `rules()` method:

```php
Field($name)
    ->rules(['alpha', 'ends_with:a']);
```

> Please note: when specifying **resource** field names in custom validation rules, you must prefix them with `record.`.

## Layout {#layout}

### Grid {#layout-grid}

By default, form fields are stacked on top of each other in one column. To change this across the entire form, you may chain the `columns()` method onto the form object:

```php
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
        ])
        ->columns(2);
}
```

Alternatively, you may customize the number of columns for a small part of the form using a Grid component:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Grid::make([
                // ...
            ])->columns(2),
        ]);
}
```

### Section {#layout-section}

You may want to separate your fields into sections, each with a heading and subheading. To do this, you can use a Section component:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Section::make(
                'Heading',
                'Subheading',
                [
                    // ...
                ],
            ),
        ]);
}
```

If you don't require a subheading, you may use the `schema()` method to declare the section schema late:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Section::make('Heading')
                ->schema([
                    // ...
                ]),
        ]);
}
```

You may use the `columns()` method to easily create a [grid](#layout-grid) within the section:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Section::make(
                'Heading',
                'Subheading',
                [
                    // ...
                ],
            )->columns(2),
        ]);
}
```

Sections may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Section::make(
                'Heading',
                'Subheading',
                [
                    // ...
                ],
            )->collapsible(),
        ]);
}
```

You may `collapse()` sections by default:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Section::make(
                'Heading',
                'Subheading',
                [
                    // ...
                ],
            )->collapsed(),
        ]);
}
```

### Fieldset {#layout-fieldset}

You may want to group fields into a Fieldset. Each fieldset has a label, a border, and a two-column grid:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Fieldset::make(
                'Label',
                [
                    // ...
                ],
            ),
        ]);
}
```

You may use the `columns()` method to customize the number of columns in the fieldset:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Fieldset::make(
                'Label',
                [
                    // ...
                ],
            )->columns(3),
        ]);
}
```

### Tabs {#layout-tabs}

Some forms can be long and complex. You may want to use tabs to reduce the number that are available at once:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Tabs::make('Label')
                ->tabs([
                    Components\Tab::make(
                        'First Tab',
                        [
                            // ...
                        ],
                    ),
                    Components\Tab::make(
                        'Second Tab',
                        [
                            // ...
                        ],
                    ),
                ]),
        ]);
}
```

You may use the `columns()` method to easily create a [grid](#layout-grid) within the tab:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Tabs::make('Label')
                ->tabs([
                    Components\Tab::make(
                        'Tab',
                        [
                            // ...
                        ],
                    )->columns(2),
                ]),
        ]);
}
```

### Group {#layout-group}

Groups are used to wrap multiple associated form components. They have no effect on the form visually, but are useful for applying modifications to many fields at once:

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Group::make([
                // ...
            ]),
        ]);
}
```

### Placeholder {#layout-placeholder}

Placeholders can be used to render text-only "fields" within your forms. Each placeholder has a value, which is cannot be changed by the user.

```php
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            // ...
            Components\Placeholder::make('website', 'filamentadmin.com'),
        ]);
}
```

## Dependent Fields {#dependent-fields}

Dependent fields are fields that are modified based on the value of another. For example, you could show a group of fields based on the value of a Select.

The first step to setting up dependent fields is to apply the `dependable()` method to the field that should be watched for changes. When the value of this field is changed, the whole form will reload:

```php
Components\Select::make('type')
    ->placeholder('Select a type')
    ->options([
        'individual' => 'Individual',
        'organization' => 'Organization',
    ])
    ->dependable();
```

To modify fields based on the value of another, you may use the `when()` method. The first argument to this method is a callback that evaluates the `$record` object, and returns true or false depending on if the modifications should be applied. The second argument makes modifications to the current field. If no second argument is supplied, the field will only be shown when the callback in the first argument is true:

In this example, the fields in the [group](#layout-group) will only be shown when the `type` field is set to `individual`:

```php
Components\Group::make([
    // ...
])->when(fn ($record) => $record->type === 'individual');
```

Here, the `company_number` field will only be required when the `type` field is set to `organization`:

```php
Components\TextInput::make('company_number')
    ->when(
        fn ($record) => $record->type === 'organization',
        fn ($field) => $field->required(),
    );
```

## Context Customization {#context-customization}

You may customize forms based on the page they are used. To do this, you can chain the `only()` or `except()` methods onto any form component.

```php
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            Components\TextInput::make('name')
                ->required()
                ->only(Pages\CreateCustomer::class),
        ]);
}
```

In this example, the `name` field will `only()` be displayed on the `CreateCustomer` page.

```php
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;

public static function form(Form $form)
{
    return $form
        ->schema([
            Components\TextInput::make('name')
                ->except(Pages\EditCustomer::class, fn ($field) => $field->required()),
        ]);
}
```

In this example, the `name` field will be required, `except()` on the `EditCustomer` page.

This is an incredibly powerful pattern, and allows you to completely customize a form contextually by chaining as many methods as you wish to the callback.

## Developing Custom Components {#custom-development}

To create a custom field, you may use:

```bash
php artisan make:filament-field CountrySelect --resource
```

This will create a new custom class and view for your field, which you may use in a form in the same way as any other field.

To create a generic form component, which may be commonly used for custom layouts, you may generate a class and view using:

```bash
php artisan make:filament-form-component SidebarLayout --resource
```

Alternatively, simple custom layouts may be created using a `View` component, and passing the name of a `$view` in your app:

```php
Components\View::make($view);
```
