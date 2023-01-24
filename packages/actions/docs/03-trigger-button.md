---
title: Trigger button
---

All actions have a trigger button. When the user clicks on it, the action is executed - a modal will open, logic will run, or they will be redirected to a URL.

This page is about customizing the look of that trigger button.

## Choosing a trigger style

Out of the box, action triggers have 3 styles - "button", "link", and "icon button".

"Button" triggers have a background color, label, and optionally an [icon](#setting-an-icon). Usually, this is the default button style, but you can use it manually with the `button()` method:

```php
Action::make('edit')
    ->button()
```

"Link" triggers have no background color. They must have a label and optionally an [icon](#setting-an-icon). They look like a link that you might find embedded within text. You can switch to that style with the `link()` method:

```php
Action::make('edit')
    ->link()
```

"Icon button" triggers are circular buttons with an [icon](#setting-an-icon) and no label. You can switch to that style with the `iconButton()` method:

```php
Action::make('edit')
    ->icon('heroicon-o-pencil-square')
    ->iconButton()
```

## Setting a label

By default, the label of the trigger button is generated from its name. You may customize this using the `label()` method:

```php
Action::make('edit')
    ->label('Edit post')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

Optionally, you can have the label automatically translated by using the `translateLabel()` method:

```php
Action::make('edit')
    ->translateLabel() // Equivalent to `label(__('Edit'))`
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

## Setting a color

Buttons may have a color to indicate their significance. It may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
Action::make('delete')
    ->action(fn () => $this->post->delete())
    ->color('danger')
```

## Setting a size

Buttons come in 3 sizes - `sm`, `md` or `lg`. You can change the size of the action's trigger using the `size()` method:

```php
Action::make('delete')
    ->action(fn () => $this->post->delete())
    ->size('lg')
```

## Setting an icon

Buttons may have an icon to add more detail to the UI. You can set the icon using the `icon()` method:

```php
Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->icon('heroicon-m-pencil-square')
```

You can also change the icon's position to be after the label instead of before it, using the `iconPosition()` method:

```php
Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
    ->icon('heroicon-m-pencil-square')
    ->iconPosition('after')
```

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

## Keybindings

You can attach keyboard shortcuts to trigger buttons. These use the same key codes as [Mousetrap](https://craig.is/killing/mice):

```php
use Filament\Actions\Action;

Action::make('save')
    ->action(fn () => $this->save())
    ->keyBindings(['command+s', 'ctrl+s'])
```