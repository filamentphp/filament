---
title: Actions
---

## Getting started

"Actions" are buttons that are displayed next to the page's heading, and allow the user to run a Livewire method on the page or visit a URL.

To define actions for a page, use the `getActions()` method:

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')->action('openSettingsModal'),
    ];
}

public function openSettingsModal(): void
{
    $this->dispatchBrowserEvent('open-settings-modal');
}
```

The button's label is generated based on it's name. To override it, you may use the `label()` method:

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')
            ->label('Settings')
            ->action('openSettingsModal'),
    ];
}
```

You may also allow the button to open a URL, using the `url()` method:

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')
            ->label('Settings')
            ->url(route('settings')),
    ];
}
```

Buttons may have a `color()`. The default is `primary`, but you may use `secondary`, `success`, `warning`, or `danger`:

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')->color('secondary'),
    ];
}
```

Buttons may have a `size()`. The default is `md`, but you may also use `sm` or `lg`:

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')->size('lg'),
    ];
}
```

Buttons may also have an `icon()`, which is the name of any Blade component. By default, the [Blade Heroicons v1](https://github.com/blade-ui-kit/blade-heroicons/tree/1.3.1) package is installed, so you may use the name of any [Heroicons v1](https://v1.heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
use Filament\Pages\Actions\Action;

protected function getActions(): array
{
    return [
        Action::make('settings')->icon('heroicon-s-cog'),
    ];
}
```

## Modals

Actions may require additional confirmation or form information before they run. You may open a modal before an action is executed to do this.

### Confirmation modals

You may require confirmation before an action is run using the `requiresConfirmation()` method. This is useful for particularly destructive actions, such as those that delete records.

```php
use Filament\Pages\Actions\Action;

Action::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
```

> Note: The confirmation modal is not available when a `url()` is set instead of an `action()`. Instead, you should redirect to the URL within the `action()` callback.

### Custom forms

You may also render a form in this modal to collect extra information from the user before the action runs.

You may use components from the [Form Builder](/docs/forms/fields) to create custom action modal forms. The data from the form is available in the `$data` array of the `action()` callback:

```php
use App\Models\User;
use Filament\Forms;
use Filament\Pages\Actions\Action;

Action::make('updateAuthor')
    ->action(function (array $data): void {
        $this->record->author()->associate($data['authorId']);
        $this->record->save();
    })
    ->form([
        Forms\Components\Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
```

#### Filling default data

You may fill the form with default data, using the `mountUsing()` method:

```php
use App\Models\User;
use Filament\Forms;
use Filament\Pages\Actions\Action;

Action::make('updateAuthor')
    ->mountUsing(fn (Forms\ComponentContainer $form) => $form->fill([
        'authorId' => $this->record->author->id,
    ]))
    ->action(function (array $data): void {
        $this->record->author()->associate($data['authorId']);
        $this->record->save();
    })
    ->form([
        Forms\Components\Select::make('authorId')
            ->label('Author')
            ->options(User::query()->pluck('name', 'id'))
            ->required(),
    ])
```

### Setting a modal heading, subheading, and button label

You may customize the heading, subheading and button label of the modal:

```php
use Filament\Pages\Actions\Action;

Action::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
    ->modalHeading('Delete posts')
    ->modalSubheading('Are you sure you\'d like to delete these posts? This cannot be undone.')
    ->modalButton('Yes, delete them')
```

### Custom content

You may define custom content to be rendered inside your modal, which you can specify by passing a Blade view into the `modalContent()` method:

```php
use Filament\Pages\Actions\Action;

Action::make('advance')
    ->action(fn () => $this->record->advance())
    ->modalContent(view('filament.pages.actions.advance'))
```

By default, the custom content is displayed above the modal form if there is one, but you can add content below using `modalFooter()` if you wish:

```php
use Filament\Pages\Actions\Action;

Action::make('advance')
    ->action(fn () => $this->record->advance())
    ->modalFooter(view('filament.pages.actions.advance'))
```

### Conditionally hiding the modal

You may have a need to conditionally show a modal for confirmation reasons while falling back to the default action. This can be achieved using `modalHidden()`:

```php
use Filament\Pages\Actions\Action;

Action::make('create')
    ->action('create')
    ->modalHidden(fn (): bool => $this->role !== 'admin')
    ->modalContent(view('filament.pages.actions.create'))
```

## Grouping

You may use an `ActionGroup` object to group multiple actions together in a dropdown:

```php
use Filament\Pages\Actions;

protected function getActions(): array
{
    return [
        Actions\ActionGroup::make([
            Actions\ViewAction::make(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]),
    ];
}
```

## Keybindings

You can attach keyboard shortcuts to actions. These use the same key codes as [Mousetrap](https://craig.is/killing/mice):

```php
use Filament\Pages\Actions\Action;

Action::make('save')
    ->action(fn () => $this->save())
    ->keyBindings(['command+s', 'ctrl+s'])
```

## Refreshing form data

If you're using actions on an [Edit](../resources/editing-records) or [View](../resources/viewing-records) resource page, you can refresh data within the main form using the `refreshFormData()` method:

```php
use Filament\Pages\Actions\Action;

Action::make('approve')
    ->action(function () {
        $this->record->approve();

        $this->refreshFormData([
            'status',
        ]);
    })
```

This method accepts an array of model attributes that you wish to refresh in the form.
