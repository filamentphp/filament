---
title: Actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Filament's schemas can use [Actions](../actions). They are buttons that can be added to any component. For instance, you may want an action to call an API endpoint to generate content with AI, or to create a new option for a select dropdown. Also, you can [render an independent sets of actions](#adding-independent-actions-to-a-schema) on their own which are not attached to a particular component.

## Defining a component action

[Action objects](../actions) are instances of `Filament/Actions/Action`. You must pass a unique name to the action's `make()` method, which is used to identify it amongst others internally within Filament. You can [customize the trigger button](../actions/trigger-button) of an action, and even [open a modal](../actions/modals) with little effort:

```php
use App\Actions\ResetStars;
use Filament\Actions\Action;

Action::make('resetStars')
    ->icon('heroicon-m-x-mark')
    ->color('danger')
    ->requiresConfirmation()
    ->action(function (ResetStars $resetStars) {
        $resetStars();
    })
```

There are a few ways you can add an action to a schema:

- You can add it as a [decoration](decorations#action-decorations) to a component. Each component has different decoration slots you could choose from.
- Particular form fields allow you to add it as an [affix action](#adding-an -affix-action-to-a-form-field) to appear conjoined to the input on either side of it.
- You can add it inside an `Actions` component to render it [independently](#adding-independent-actions-to-a-schema) from other components.

## Adding an affix action to a form field

Certain fields support "affix actions", which are buttons that can be placed before or after its input area. The following fields support affix actions:

- [Text input](../forms/fields/text-input)
- [Select](../forms/fields/select)
- [Date-time picker](../forms/fields/date-time-picker)
- [Color picker](../forms/fields/color-picker)

To define an affix action, you can pass it to either `prefixAction()` or `suffixAction()`:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;

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

<AutoScreenshot name="forms/fields/actions/suffix" alt="Text input with suffix action" version="4.x" />

Notice `$set` and `$state` injected into the `action()` function in this example. This is [form component action utility injection](#component-action-utility-injection).

#### Passing multiple affix actions to a form field

You may pass multiple affix actions to a field by passing them in an array to either `prefixActions()` or `suffixActions()`. Either method can be used, or both at once, Filament will render all the registered actions in order:

```php
use Filament\Actions\Action;
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

## Adding independent actions to a schema

You may use an `Actions` component to render a set of actions anywhere in the schema, avoiding the need to register them to any particular component:

```php
use App\Actions\ResetStars;
use App\Actions\Star;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;

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

<AutoScreenshot name="schemas/layout/actions/independent/simple" alt="Independent actions" version="4.x" />

### Making independent schema actions consume the full width of the schema

You can stretch the independent schema actions to consume the full width of the schema using `fullWidth()`:

```php
use Filament\Schemas\Components\Actions;

Actions::make([
    // ...
])->fullWidth(),
```

<AutoScreenshot name="schemas/layout/actions/independent/full-width" alt="Independent actions consuming the full width" version="4.x" />

### Controlling the horizontal alignment of independent schema actions

Independent schema actions are aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `alignment()`:

```php
use Filament\Schemas\Components\Actions;
use Filament\Support\Enums\Alignment;

Actions::make([
    // ...
])->alignment(Alignment::Center),
```

<AutoScreenshot name="schemas/layout/actions/independent/horizontally-aligned-center" alt="Independent actions horizontally aligned to the center" version="4.x" />

### Controlling the vertical alignment of independent schema actions

Independent schema actions are vertically aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `verticalAlignment()`:

```php
use Filament\Schemas\Components\Actions;
use Filament\Support\Enums\VerticalAlignment;

Actions::make([
    // ...
])->verticalAlignment(VerticalAlignment::End),
```

<AutoScreenshot name="schemas/layout/actions/independent/vertically-aligned-end" alt="Independent actions vertically aligned to the end" version="4.x" />

## Adding an action to a custom schema component

If you wish to render an action within a custom schema component, `ViewField` object, or `View` component object, you may do so using the `registerActions()` method:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\ViewField;

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

Notice `$set` injected into the `action()` function in this example. This is [schema component action utility injection](#component-action-utility-injection).

Now, to render the action in the view of the custom component, you need to call `$getAction()`, passing the name of the action you registered:

```blade
<div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
    <input x-model="state" type="range" />
    
    {{ $getAction('setMaximum') }}
</div>
```

## Component action utility injection

An action is able to [inject utilities](../forms/advanced#form-component-utility-injection) from the component it is attached to, inside the `action()` function. For instance, you can inject [`$set`](advanced#injecting-a-function-to-set-the-state-of-another-field) and [`$state`](advanced#injecting-the-current-state-of-a-field):

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;

Action::make('copyCostToPrice')
    ->icon('heroicon-m-clipboard')
    ->requiresConfirmation()
    ->action(function (Set $set, $state) {
        $set('price', $state);
    })
```

Schema component actions also have access to [all utilities that apply to actions](../actions/advanced#action-utility-injection) in general.
