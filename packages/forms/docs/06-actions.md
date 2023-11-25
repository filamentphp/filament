---
title: Actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Filament's forms can use [Actions](../actions). They are buttons that can be added to any form component. For instance, you may want an action to call an API endpoint to generate content with AI, or to create a new option for a select dropdown. Also, you can [render anonymous sets of actions](#adding-anonymous-actions-to-a-form-without-attaching-them-to-a-component) on their own which are not attached to a particular form component.

## Defining a form component action

Action objects inside a form component are instances of `Filament/Forms/Components/Actions/Action`. You must pass a unique name to the action's `make()` method, which is used to identify it amongst others internally within Filament. You can [customize the trigger button](../actions/trigger-button) of an action, and even [open a modal](../actions/modals) with little effort:

```php
use App\Actions\ResetStars;
use Filament\Forms\Components\Actions\Action;

Action::make('resetStars')
    ->icon('heroicon-m-x-mark')
    ->color('danger')
    ->requiresConfirmation()
    ->action(function (ResetStars $resetStars) {
        $resetStars();
    })
```

### Adding an affix action to a field

Certain fields support "affix actions", which are buttons that can be placed before or after its input area. The following fields support affix actions:

- [Text input](fields/text-input)
- [Select](fields/select)
- [Date-time picker](fields/date-time-picker)
- [Color picker](fields/color-picker)

To define an affix action, you can pass it to either `prefixAction()` or `suffixAction()`:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;

TextInput::make('cost')
    ->prefix('€')
    ->suffixAction(
        Action::make('copyCostToPrice')
            ->icon('heroicon-m-clipboard')
            ->requiresConfirmation()
            ->action(function (Set $set, $state) {
                $set('price', $state);
            })
    )
```

<AutoScreenshot name="forms/fields/actions/suffix" alt="Text input with suffix action" version="3.x" />

Notice `$set` and `$state` injected into the `action()` function in this example. This is [form component action utility injection](#form-component-action-utility-injection).

#### Passing multiple affix actions to a field

You may pass multiple affix actions to a field by passing them in an array to either `prefixActions()` or `suffixActions()`. Either method can be used, or both at once, Filament will render all the registered actions in order:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;

TextInput::make('cost')
    ->prefix('€')
    ->prefixActions([
        Action::make('...'),
        Action::make('...'),
        Action::make('...'),
    ])
    ->suffixActions([
        Action::make('...'),
        Action::make('...'),
    ])
```

### Adding a hint action to a field

All fields support "hint actions", which are rendered aside the field's [hint](fields/getting-started#adding-a-hint-next-to-the-label). To add a hint action to a field, you may pass it to `hintAction()`:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;

TextInput::make('cost')
    ->prefix('€')
    ->hintAction(
        Action::make('copyCostToPrice')
            ->icon('heroicon-m-clipboard')
            ->requiresConfirmation()
            ->action(function (Set $set, $state) {
                $set('price', $state);
            })
    )
```

Notice `$set` and `$state` injected into the `action()` function in this example. This is [form component action utility injection](#form-component-action-utility-injection).

<AutoScreenshot name="forms/fields/actions/hint" alt="Text input with hint action" version="3.x" />

#### Passing multiple hint actions to a field

You may pass multiple hint actions to a field by passing them in an array to `hintActions()`. Filament will render all the registered actions in order:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;

TextInput::make('cost')
    ->prefix('€')
    ->hintActions([
        Action::make('...'),
        Action::make('...'),
        Action::make('...'),
    ])
```

### Adding an action to a custom form component

If you wish to render an action within a custom form component, `ViewField` object, or `View` component object, you may do so using the `registerActions()` method:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Set;

ViewField::make('rating')
    ->view('filament.forms.components.range-slider')
    ->registerActions([
        Action::make('setMaximum')
            ->icon('heroicon-m-star')
            ->action(function (Set $set) {
                $set('rating', 5);
            }),
    ])
```

Notice `$set` injected into the `action()` function in this example. This is [form component action utility injection](#form-component-action-utility-injection).

Now, to render the action in the view of the custom component, you need to call `$getAction()`, passing the name of the action you registered:

```blade
<div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
    <input x-model="state" type="range" />
    
    {{ $getAction('setMaximum') }}
</div>
```

### Adding "anonymous" actions to a form without attaching them to a component

You may use an `Actions` component to render a set of actions anywhere in the form, avoiding the need to register them to any particular component:

```php
use App\Actions\Star;
use App\Actions\ResetStars;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;

Actions::make([
    Action::make('star')
        ->icon('heroicon-m-star')
        ->requiresConfirmation()
        ->action(function (Star $star) {
            $star();
        }),
    Action::make('resetStars')
        ->icon('heroicon-m-x-mark')
        ->color('danger')
        ->requiresConfirmation()
        ->action(function (ResetStars $resetStars) {
            $resetStars();
        }),
]),
```

<AutoScreenshot name="forms/layout/actions/anonymous/simple" alt="Anonymous actions" version="3.x" />

#### Making the independent form actions consume the full width of the form

You can stretch the independent form actions to consume the full width of the form using `fullWidth()`:

```php
use Filament\Forms\Components\Actions;

Actions::make([
    // ...
])->fullWidth(),
```

<AutoScreenshot name="forms/layout/actions/anonymous/full-width" alt="Anonymous actions consuming the full width" version="3.x" />

#### Controlling the horizontal alignment of independent form actions

Independent form actions are aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `alignment()`:

```php
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\Alignment;

Actions::make([
    // ...
])->alignment(Alignment::Center),
```

<AutoScreenshot name="forms/layout/actions/anonymous/horizontally-aligned-center" alt="Anonymous actions horizontally aligned to the center" version="3.x" />

#### Controlling the vertical alignment of independent form actions

Independent form actions are vertically aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `verticalAlignment()`:

```php
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\VerticalAlignment;

Actions::make([
    // ...
])->verticalAlignment(VerticalAlignment::End),
```

<AutoScreenshot name="forms/layout/actions/anonymous/vertically-aligned-end" alt="Anonymous actions vertically aligned to the end" version="3.x" />

## Form component action utility injection

If an action is attached to a form component, the `action()` function is able to [inject utilities](advanced#form-component-utility-injection) directly from that form component. For instance, you can inject [`$set`](advanced#injecting-a-function-to-set-the-state-of-another-field) and [`$state`](advanced#injecting-the-current-state-of-a-field):

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;

Action::make('copyCostToPrice')
    ->icon('heroicon-m-clipboard')
    ->requiresConfirmation()
    ->action(function (Set $set, $state) {
        $set('price', $state);
    })
```

Form component actions also have access to [all utilities that apply to actions](../actions/advanced#action-utility-injection) in general.
