---
title: Pages
---

Filament allows you to create completely custom pages for the admin panel.

## Getting started

To create a new page, you can use:

```bash
php artisan make:filament-page Settings
```

This command will create two files - a page class in the `/Pages` directory of the Filament directory, and a view in the `/pages` directory of the Filament views directory.

Page classes are all full-page [Livewire](https://laravel-livewire.com) components with a few extra utilities you can use with the admin panel.

## Actions

"Actions" are buttons that are displayed next to the page's heading, and allow the user to run a Livewire method on the page or visit a URL.

To define actions for a page, use the `getActions()` method:

```php
protected function getActions(): array
{
    return [
        ButtonAction::make('settings')->action('openSettingsModal'),
    ];
}

public function openSettingsModal(): void
{
    $this->dispatchBrowserEvent('open-settings-modal');
}
```

The button's label is generated based on it's name. To override it, you may use the `label()` method:

```php
protected function getActions(): array
{
    return [
        ButtonAction::make('settings')
            ->label('Settings')
            ->action('openSettingsModal'),
    ];
}
```

You may also allow the button to open a URL, using the `url()` method:

```php
protected function getActions(): array
{
    return [
        ButtonAction::make('settings')
            ->label('Settings')
            ->url(route('settings')),
    ];
}
```

Buttons may have a `color()`. The default is `primary`, but you may use `secondary`, `success`, `warning`, or `danger`:

```php
protected function getActions(): array
{
    return [
        ButtonAction::make('settings')->color('secondary'),
    ];
}
```

Buttons may also have an `icon()`, which is the name of any Blade component. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
protected function getActions(): array
{
    return [
        ButtonAction::make('settings')->icon('heroicon-s-cog'),
    ];
}
```

### Modals

Actions may require additional confirmation or form information before they run. You may open a modal before an action is executed to do this.

#### Confirmation modals

You may require confirmation before an action is run using the `requiresConfirmation()` method. This is useful for particularly destructive actions, such as those that delete records.

```php
use Filament\Pages\Actions\ButtonAction;

ButtonAction::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
```

> Note: The confirmation modal is not available when a `url()` is set instead of an `action()`. Instead, you should redirect to the URL within the `action()` callback.

#### Custom forms

You may also render a form in this modal to collect extra information from the user before the action runs.

You may use components from the [Form Builder](/docs/forms/fields) to create custom action modal forms. The data from the form is available in the `$data` array of the `action()` callback:

```php
use App\Modals\User;
use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

ButtonAction::make('updateAuthor')
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

#### Setting a modal heading, subheading, and button label

You may customize the heading, subheading and button label of the modal:

```php
use Filament\Pages\Actions\ButtonAction;

ButtonAction::make('delete')
    ->action(fn () => $this->record->delete())
    ->requiresConfirmation()
    ->modalHeading('Delete posts')
    ->modalSubheading('Are you sure you\'d like to delete these posts? This cannot be undone.')
    ->modalButton('Yes, delete them')
```


## Conditionally hiding pages in navigation

You can prevent pages from appearing in the menu by overriding the `shouldRegisterNavigation()` method in your Page class. This is useful if you want to control which users can see the page in the sidebar.

```php
protected static function shouldRegisterNavigation(): bool
{
    return auth()->user()->canManageSettings();
}
```

Please be aware that all users will still be able to visit this page through its direct URL, so to fully limit access you must also also check in the `mount()` method of the page:

```php
public function mount(): void
{
    abort_unless(auth()->user()->canManageSettings(), 403);
}
```

## Building widgets

Filament allows you to display widgets inside pages, below the header and above the footer.

To register a widget on a page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods:

```php
use App/Filament/Widgets/StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::class
    ];
}
```

## Sending flash notifications

You can send flash notifications to the user from each page by calling the `notify()` method on the page or relation manager class:

```php
$this->notify('success', 'Saved');
```

There are four types of notifications available, each with a different color and icon:

 - `primary` - for providing information.
 - `danger` - for reporting errors.
 - `success` - for success messages.
 - `warning` - for reporting non-critical issues.

By default, notifications will be sent to the user immediately. If you'd like to wait until a redirect is complete, you can use the `isAfterRedirect` argument:

```php
$this->notify('success', 'Created', isAfterRedirect: true);
```

Alternatively, you can call `Filament::notify()` from anywhere in your app, and pass the same arguments:

```php
use Filament\Facades\Filament;

Filament::notify('success', 'Saved');
```

## Customization

Filament will automatically generate a title, navigation label and URL (slug) for your page based on its name. You may override it using static properties of your page class:

```php
protected static ?string $title = 'Custom Page Title';

protected static ?string $navigationLabel = 'Custom Navigation Label';

protected static ?string $slug = 'custom-url-slug';
```

You may also specify a custom header and footer view for any page. You may return them from the `getHeader()` and `getFooter()` methods:

```php

use Illuminate\Contracts\View\View;

protected function getHeader(): View
{
    return view('filament.settings.custom-header');
}

protected function getFooter(): View
{
    return view('filament.settings.custom-footer');
}
```
