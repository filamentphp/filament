---
title: Modals
---
import PreviewScreenshot from "@components/PreviewScreenshot.astro"

Actions may require additional confirmation or input from the user before they run. You may open a modal before an action is executed to do this.

## Confirmation modals

You may require confirmation before an action is run using the `requiresConfirmation()` method. This is useful for particularly destructive actions, such as those that delete records.

```php
Action::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
```

<PreviewScreenshot name="actions/modal/confirmation" alt="Confirmation modal" version="3.x" />

> Note: The confirmation modal is not available when a `url()` is set instead of an `action()`. Instead, you should redirect to the URL within the `action()` callback.

## Setting a modal heading, subheading, and button label

You may customize the heading, subheading and button label of the modal:

```php
Action::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
    ->modalHeading('Delete post')
    ->modalSubheading('Are you sure you\'d like to delete this post? This cannot be undone.')
    ->modalButton('Yes, delete it')
```

<PreviewScreenshot name="actions/modal/confirmation-custom-text" alt="Confirmation modal with custom text" version="3.x" />

## Modal forms

You may also render a form in the modal to collect extra information from the user before the action runs.

You may use components from the [Form Builder](../forms) to create custom action modal forms. The data from the form is available in the `$data` array of the `action()` callback:

```php
use App\Models\User;
use Filament\Forms\Components\Select;

Action::make('updateAuthor')
    ->form([
        Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
    ->action(function (array $data): void {
        $this->record->author()->associate($data['authorId']);
        $this->record->save();
    })
```

<PreviewScreenshot name="actions/modal/form" alt="Modal with form" version="3.x" />

### Filling the form with existing data

You may fill the form with existing data, using the `fillForm()` method:

```php
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

Action::make('updateAuthor')
    ->fillForm([
        'authorId' => $this->record->author->id,
    ])
    ->form([
        Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
    ->action(function (array $data): void {
        $this->record->author()->associate($data['authorId']);
        $this->record->save();
    })
```

### Using a wizard as a modal form

You may create a [multi-step form wizard](../forms/layout/wizard) inside a modal. Instead of using a `form()`, define a `steps()` array and pass your `Step` objects:

```php
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;

Action::make('create')
    ->steps([
        Step::make('Name')
            ->description('Give the category a unique name')
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->disabled()
                    ->required()
                    ->unique(Category::class, 'slug'),
            ])
            ->columns(2),
        Step::make('Description')
            ->description('Add some extra details')
            ->schema([
                MarkdownEditor::make('description'),
            ]),
        Step::make('Visibility')
            ->description('Control who can view it')
            ->schema([
                Toggle::make('is_visible')
                    ->label('Visible to customers.')
                    ->default(true),
            ]),
    ])
```

<PreviewScreenshot name="actions/modal/wizard" alt="Modal with wizard" version="3.x" />

### Disabling all form fields

You may wish to disable all form fields in the modal, ensuring the user cannot edit them. You may do so using the `disabledForm()` method:

```php
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

Action::make('approvePost')
    ->form([
        TextInput::make('title'),
        Textarea::make('content'),
    ])
    ->fillForm([
        'title' => $this->record->title,
        'content' => $this->record->content,
    ])
    ->disabledForm()
    ->action(function (): void {
        $this->record->approve();
    })
```

## Custom modal content

You may define custom content to be rendered inside your modal, which you can specify by passing a Blade view into the `modalContent()` method:

```php
Action::make('advance')
    ->action(fn () => $this->record->advance())
    ->modalContent(view('filament.pages.actions.advance'))
```

By default, the custom content is displayed above the modal form if there is one, but you can add content below using `modalFooter()` if you wish:

```php
Action::make('advance')
    ->action(fn () => $this->record->advance())
    ->modalFooter(view('filament.pages.actions.advance'))
```

## Using a slide-over instead of a modal

You can open a "slide-over" dialog instead of a modal by using the `slideOver()` method:

```php
Action::make('updateAuthor')
    ->form([
        // ...
    ])
    ->action(function (array $data): void {
        // ...
    })
    ->slideOver()
```

<PreviewScreenshot name="actions/modal/slide-over" alt="Slide over with form" version="3.x" />

Instead of opening in the center of the screen, the modal content will now slide in from the right and consume the entire height of the browser.

## Changing the modal width

You can change the width of the modal by using the `modalWidth()` method. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, `7xl`, and `screen`:

```php
Action::make('updateAuthor')
    ->form([
        // ...
    ])
    ->action(function (array $data): void {
        // ...
    })
    ->modalWidth('5xl')
```

## Conditionally hiding the modal

You may have a need to conditionally show a modal for confirmation reasons while falling back to the default action. This can be achieved using `modalHidden()`:

```php
Action::make('create')
    ->action('create')
    ->modalHidden(fn (): bool => $this->role !== 'admin')
    ->modalContent(view('filament.pages.actions.create'))
```

## Executing code when the modal opens

You may execute code within a closure when the modal opens, by passing it to the `mountUsing()` method:

```php
use Filament\Forms\Form;

Action::make('create')
    ->action('create')
    ->mountUsing(function (Form $form) {
        $form->fill();
        
        // ...
    })
```

Please note that the `mountUsing()` method, by default, is used by Filament to initialize the [form](#modal-forms). If you override this method, you will need to call `$form->fill()` to ensure the form is initialized correctly. If you wish to populate the form with data, you can do so by passing an array to the `fill()` method, instead of [using `fillForm()` on the action itself](#filling-the-form-with-existing-data).

## Centering modal content

By default, the modal's content will be centered if the modal is `xs` or `sm` in [width](#changing-the-modal-width). If you wish to center the content of a larger modal, you can use the `centerModal()` method:

```php
Action::make('updateAuthor')
    ->form([
        // ...
    ])
    ->action(function (array $data): void {
        // ...
    })
    ->centerModal()
```

## Closing the modal by clicking away

By default, when you click away from a modal, it will close itself. If you wish to disable this behavior for a specific action, you can use the `closeModalByClickingAway(false)` method:

```php
Action::make('updateAuthor')
    ->form([
        // ...
    ])
    ->action(function (array $data): void {
        // ...
    })
    ->closeModalByClickingAway(false)
```

If you'd like to change the behaviour for all modals in the application, you can do so by calling `Modal::closedByClickingAway()` inside a service provider or middleware:

```php
use Filament\Support\View\Components\Modal;

Modal::closedByClickingAway(false);
```

## Customizing the action buttons in the footer of the modal

By default, there are two actions in the footer of a modal. The first is a button to submit, which executes the `action()`. The second button closes the modal and cancels the action.

### Modifying a default modal action button

To modify the action instance that is used to render one of the default action buttons, you may pass a closure to the `modalSubmitAction()` and `modalCancelAction()` methods:

```php
use Filament\Actions\Modal\Actions\Action as ModalAction;

Action::make('help')
    ->modalContent(view('actions.help'))
    ->modalCancelAction(fn (ModalAction $action) => $action->label('Close'))
```

The [methods available to customize trigger buttons](trigger-button) will work to modify on the `$action` instance inside the closure.

### Removing a default modal action button

To remove a default action, you may pass `false` to either `modalSubmitAction()` or `modalCancelAction()`:

```php
Action::make('help')
    ->modalContent(view('actions.help'))
    ->modalSubmitAction(false)
```

### Adding an extra modal action button

You may pass an array of extra actions to be rendered, between the default actions, in the footer of the modal using the `extraModalActions()` method:

```php
Action::make('create')
    ->extraModalActions(fn (Action $action): array => [
        $action->makeExtraModalAction('createAnother', ['another' => true]),
    ])
```

`$action->makeExtraModalAction()` returns an action instance that can be customized using the [methods available to customize trigger buttons](trigger-button).

The second parameter of `makeExtraModalAction()` allows you to pass an array of arguments that will be accessible inside the action's `action()` closure as `$arguments`. These could be useful as flags to indicate that the action should behave differently based on the user's decision:

```php
Action::make('create')
    ->extraModalActions(fn (Action $action): array => [
        $action->makeExtraModalAction('createAnother', ['another' => true]),
    ])
    ->action(function (array $data, array $arguments): void {
        // Create
    
        if ($arguments['another'] ?? false) {
            // Reset the form and don't close the modal
        }
    })
```
