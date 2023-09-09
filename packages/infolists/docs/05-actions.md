---
title: Actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Filament's infolists can use [Actions](../actions). They are buttons that can be added to any infolist component. Also, you can [render anonymous sets of actions](#adding-anonymous-actions-to-an-infolist-without-attaching-them-to-a-component) on their own, that are not attached to a particular infolist component.

## Defining a infolist component action

Action objects inside an infolist component are instances of `Filament/Infolists/Components/Actions/Action`. You must pass a unique name to the action's `make()` method, which is used to identify it amongst others internally within Filament. You can [customize the trigger button](../actions/trigger-button) of an action, and even [open a modal](../actions/modals) with little effort:

```php
use App\Actions\ResetStars;
use Filament\Infolists\Components\Actions\Action;

Action::make('resetStars')
    ->icon('heroicon-m-x-mark')
    ->color('danger')
    ->requiresConfirmation()
    ->action(function (ResetStars $resetStars) {
        $resetStars();
    })
```

### Adding an affix action to a entry

Certain entries support "affix actions", which are buttons that can be placed before or after its content. The following entries support affix actions:

- [Text entry](entries/text-entry)

To define an affix action, you can pass it to either `prefixAction()` or `suffixAction()`:

```php
use App\Models\Product;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;

TextEntry::make('cost')
    ->prefix('€')
    ->suffixAction(
        Action::make('copyCostToPrice')
            ->icon('heroicon-m-clipboard')
            ->requiresConfirmation()
            ->action(function (Product $record) {
                $record->price = $record->cost;
                $record->save();
            })
    )
```

<AutoScreenshot name="infolists/entries/actions/suffix" alt="Text entry with suffix action" version="3.x" />

#### Passing multiple affix actions to a entry

You may pass multiple affix actions to an entry by passing them in an array to either `prefixActions()` or `suffixActions()`. Either method can be used, or both at once, Filament will render all the registered actions in order:

```php
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;

TextEntry::make('cost')
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

### Adding a hint action to an entry

All entries support "hint actions", which are rendered aside the entry's [hint](entries/getting-started#adding-a-hint-next-to-the-label). To add a hint action to a entry, you may pass it to `hintAction()`:

```php
use App\Models\Product;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;

TextEntry::make('cost')
    ->prefix('€')
    ->hintAction(
        Action::make('copyCostToPrice')
            ->icon('heroicon-m-clipboard')
            ->requiresConfirmation()
            ->action(function (Product $record) {
                $record->price = $record->cost;
                $record->save();
            })
    )
```

<AutoScreenshot name="infolists/entries/actions/hint" alt="Text entry with hint action" version="3.x" />

#### Passing multiple hint actions to a entry

You may pass multiple hint actions to a entry by passing them in an array to `hintActions()`. Filament will render all the registered actions in order:

```php
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;

TextEntry::make('cost')
    ->prefix('€')
    ->hintActions([
        Action::make('...'),
        Action::make('...'),
        Action::make('...'),
    ])
```

### Adding an action to a custom infolist component

If you wish to render an action within a custom infolist component, `ViewEntry` object, or `View` component object, you may do so using the `registerActions()` method:

```php
use App\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Set;

ViewEntry::make('status')
    ->view('filament.infolists.entries.status-switcher')
    ->registerActions([
        Action::make('createStatus')
            ->form([
                TextInput::make('name')
                    ->required(),
            ])
            ->icon('heroicon-m-plus')
            ->action(function (array $data, Post $record) {
                $record->status()->create($data);
            }),
    ])
```

Now, to render the action in the view of the custom component, you need to call `$getAction()`, passing the name of the action you registered:

```blade
<div>
    <select></select>
    
    {{ $getAction('createStatus') }}
</div>
```

### Adding "anonymous" actions to an infolist without attaching them to a component

You may use an `Actions` component to render a set of actions anywhere in the form, avoiding the need to register them to any particular component:

```php
use App\Actions\Star;
use App\Actions\ResetStars;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;

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

<AutoScreenshot name="infolists/layout/actions/anonymous/simple" alt="Anonymous actions" version="3.x" />

#### Making the independent infolist actions consume the full width of the infolist

You can stretch the independent infolist actions to consume the full width of the infolist using `fullWidth()`:

```php
use Filament\Infolists\Components\Actions;

Actions::make([
    // ...
])->fullWidth(),
```

<AutoScreenshot name="infolists/layout/actions/anonymous/full-width" alt="Anonymous actions consuming the full width" version="3.x" />

#### Controlling the horizontal alignment of independent infolist actions

Independent infolist actions are aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `alignment()`:

```php
use Filament\Infolists\Components\Actions;
use Filament\Support\Enums\Alignment;

Actions::make([
    // ...
])->alignment(Alignment::Center),
```

<AutoScreenshot name="infolists/layout/actions/anonymous/horizontally-aligned-center" alt="Anonymous actions horizontally aligned to the center" version="3.x" />

#### Controlling the vertical alignment of independent infolist actions

Independent infolist actions are vertically aligned to the start of the component by default. You may change this by passing `Alignment::Center` or `Alignment::End` to `verticalAlignment()`:

```php
use Filament\Infolists\Components\Actions;
use Filament\Support\Enums\VerticalAlignment;

Actions::make([
    // ...
])->verticalAlignment(VerticalAlignment::End),
```

<AutoScreenshot name="infolists/layout/actions/anonymous/vertically-aligned-end" alt="Anonymous actions vertically aligned to the end" version="3.x" />
