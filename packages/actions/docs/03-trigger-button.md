---
title: Trigger button
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

All actions have a trigger button. When the user clicks on it, the action is executed - a modal will open, a closure function will be executed, or they will be redirected to a URL.

This page is about customizing the look of that trigger button.

## Choosing a trigger style

Out of the box, action triggers have 4 styles - "button", "link", "icon button", and "badge".

"Button" triggers have a background color, label, and optionally an [icon](#setting-an-icon). Usually, this is the default button style, but you can use it manually with the `button()` method:

```php
Action::make('edit')
    ->button()
```

<AutoScreenshot name="actions/trigger-button/button" alt="Button trigger" version="3.x" />

"Link" triggers have no background color. They must have a label and optionally an [icon](#setting-an-icon). They look like a link that you might find embedded within text. You can switch to that style with the `link()` method:

```php
Action::make('edit')
    ->link()
```

<AutoScreenshot name="actions/trigger-button/link" alt="Link trigger" version="3.x" />

"Icon button" triggers are circular buttons with an [icon](#setting-an-icon) and no label. You can switch to that style with the `iconButton()` method:

```php
Action::make('edit')
    ->icon('heroicon-m-pencil-square')
    ->iconButton()
```

<AutoScreenshot name="actions/trigger-button/icon-button" alt="Icon button trigger" version="3.x" />

"Badge" triggers have a background color, label, and optionally an [icon](#setting-an-icon). You can use a badge as trigger using the `badge()` method:

```php
Action::make('edit')
    ->badge()
```

<AutoScreenshot name="actions/trigger-button/badge" alt="Badge trigger" version="3.x" />

### Using an icon button on mobile devices only

You may want to use a button style with a label on desktop, but remove the label on mobile. This will transform it into an icon button. You can do this with the `labeledFrom()` method, passing in the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) at which you want the label to be added to the button:

```php
Action::make('edit')
    ->icon('heroicon-m-pencil-square')
    ->button()
    ->labeledFrom('md')
```

## Setting a label

By default, the label of the trigger button is generated from its name. You may customize this using the `label()` method:

```php
Action::make('edit')
    ->label('Edit post')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

Optionally, you can have the label automatically translated [using Laravel's localization features](https://laravel.com/docs/localization) with the `translateLabel()` method:

```php
Action::make('edit')
    ->translateLabel() // Equivalent to `label(__('Edit'))`
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

## Setting a color

Buttons may have a color to indicate their significance. It may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`:

```php
Action::make('delete')
    ->color('danger')
```

<AutoScreenshot name="actions/trigger-button/danger" alt="Red trigger" version="3.x" />

## Setting a size

Buttons come in 3 sizes - `ActionSize::Small`, `ActionSize::Medium` or `ActionSize::Large`. You can change the size of the action's trigger using the `size()` method:

```php
use Filament\Support\Enums\ActionSize;

Action::make('create')
    ->size(ActionSize::Large)
```

<AutoScreenshot name="actions/trigger-button/large" alt="Large trigger" version="3.x" />

## Setting an icon

Buttons may have an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to add more detail to the UI. You can set the icon using the `icon()` method:

```php
Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->icon('heroicon-m-pencil-square')
```

<AutoScreenshot name="actions/trigger-button/icon" alt="Trigger with icon" version="3.x" />

You can also change the icon's position to be after the label instead of before it, using the `iconPosition()` method:

```php
use Filament\Support\Enums\IconPosition;

Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->icon('heroicon-m-pencil-square')
    ->iconPosition(IconPosition::After)
```

<AutoScreenshot name="actions/trigger-button/icon-after" alt="Trigger with icon after the label" version="3.x" />

## Authorization

You may conditionally show or hide actions for certain users. To do this, you can use either the `visible()` or `hidden()` methods:

```php
Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->visible(auth()->user()->can('update', $this->post))

Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->hidden(! auth()->user()->can('update', $this->post))
```

This is useful for authorization of certain actions to only users who have permission.

### Disabling a button

If you want to disable a button instead of hiding it, you can use the `disabled()` method:

```php
Action::make('delete')
    ->disabled()
```

You can conditionally disable a button by passing a boolean to it:

```php
Action::make('delete')
    ->disabled(! auth()->user()->can('delete', $this->post))
```

## Registering keybindings

You can attach keyboard shortcuts to trigger buttons. These use the same key codes as [Mousetrap](https://craig.is/killing/mice):

```php
use Filament\Actions\Action;

Action::make('save')
    ->action(fn () => $this->save())
    ->keyBindings(['command+s', 'ctrl+s'])
```

## Adding a badge to the corner of the button

You can add a badge to the corner of the button, to display whatever you want. It's useful for displaying a count of something, or a status indicator:

```php
use Filament\Actions\Action;

Action::make('filter')
    ->iconButton()
    ->icon('heroicon-m-funnel')
    ->badge(5)
```

<AutoScreenshot name="actions/trigger-button/badged" alt="Trigger with badge" version="3.x" />

You can also pass a color to be used for the badge, which can be either `danger`, `gray`, `info`, `primary`, `success` and `warning`:

```php
use Filament\Actions\Action;

Action::make('filter')
    ->iconButton()
    ->icon('heroicon-m-funnel')
    ->badge(5)
    ->badgeColor('success')
```

<AutoScreenshot name="actions/trigger-button/success-badged" alt="Trigger with green badge" version="3.x" />

## Outlined button style

When you're using the "button" trigger style, you might wish to make it less prominent. You could use a different [color](#setting-a-color), but sometimes you might want to make it outlined instead. You can do this with the `outlined()` method:

```php
use Filament\Actions\Action;

Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->button()
    ->outlined()
```

<AutoScreenshot name="actions/trigger-button/outlined" alt="Outlined trigger button" version="3.x" />

## Adding extra HTML attributes

You can pass extra HTML attributes to the button which will be merged onto the outer DOM element. Pass an array of attributes to the `extraAttributes()` method, where the key is the attribute name and the value is the attribute value:

```php
use Filament\Actions\Action;

Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->extraAttributes([
        'title' => 'Edit this post',
    ])
```

If you pass CSS classes in a string, they will be merged with the default classes that already apply to the other HTML element of the button:

```php
use Filament\Actions\Action;

Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->extraAttributes([
        'class' => 'mx-auto my-8',
    ])
```
