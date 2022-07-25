---
title: Sending notifications
---

Notifications are sent using a `Notification` object that's constructed through a fluent API. Calling the `->send()` method on the `Notification` object will dispatch the notification and display it in your application. As the session is used to flash notifications, they can be sent from anywhere in your code, not just Livewire components.

```php
<?php

namespace App\Http\Livewire;

use Filament\Notifications\Notification; // [tl! focus]
use Livewire\Component;

class EditPost extends Component
{
    public function save(): void
    {
        Notification::make() // [tl! focus:start]
            ->title('Saved successfully')
            ->succes()
            ->send(); // [tl! focus:end]
    }
}

```

## Title

The main message of the notification is shown in the title. You can set the title as follows:

```php
Notification::make()
    ->title('Saved successfully'); // [tl! focus]
```

Markdown text will automatically be rendered if passed to the title.

## Icon

Optionally, a notification can have an icon that's displayed in front of its content. You may also set a color for the icon, which defaults to the `secondary` color specified in your `tailwind.config.js` file.

```php
Notification::make()
    ->title('Saved successfully')
    ->icon('heroicon-o-check-circle') // [tl! focus:start]
    ->iconColor('success'); // [tl! focus:end]
```

Notifications often have a status like `success`, `warning` or `danger`. Instead of manually setting the corresponding icons and colors, there's a `->status()` method which you can pass the status. You may also use the dedicated `->success()`, `->warning()` and `->danger()` methods instead. So, cleaning up the above example would look like this:

```php
Notification::make()
    ->title('Saved successfully')
    ->success(); // [tl! focus]
```

## Duration

By default, notifications are shown for 6 seconds before they're automatically closed. You may specify a custom duration value in milliseconds as follows:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->duration(5000); // [tl! focus]
```

If you prefer setting a duration in seconds instead of milliseconds, you can do so:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->seconds(5); // [tl! focus]
```

You might want some notifications to not automatically close and require the user to close them manually. This can be achieved by making the notification persistent:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->persistent(); // [tl! focus]
```

## Body

Additional notification text can be shown in the body. Similar to the title, it supports Markdown:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.'); // [tl! focus]
```

## Actions

Notifications support actions that render a button or link which may open a URL or emit a Livewire event. Actions will render as link by default, but you may configure it to render a button using the `->button()` method. Actions can be defined as follows:

```php
use Filament\Notifications\Actions\Action; // [tl! focus]

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([ // [tl! focus:start]
        Action::make('show')
            ->button(),
        Action::make('undo')
            ->color('danger'),
    ]); // [tl! focus:end]
```

### Opening URLs

If clicking on an action should open a URL, optionally in a new tab, you can do so:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('show')
            ->button()
            ->url(route('posts.show', $post)) // [tl! focus:start]
            ->openUrlInNewTab(), // [tl! focus:end]
        Action::make('undo')
            ->color('danger'),
    ]);
```

### Emitting events

Sometimes you want to execute additional code when a notification action is clicked. This can be achieved by setting a Livewire event which should be emitted on clicking the action. You may optionally pass an array of data, which will be available as parameters in the event listener on your Livewire component.

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('show')
            ->button()
            ->url(route('posts.show', $post))
            ->openUrlInNewTab(),
        Action::make('undo')
            ->color('danger')
            ->emit('undoEditingPost', [$post->id]), // [tl! focus]
    ]);
```

### Closing notifications

After opening a URL or emitting an event from your action, you may want to close the notification right away:

```php
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('show')
            ->button()
            ->url(route('posts.show', $post))
            ->openUrlInNewTab(),
        Action::make('undo')
            ->color('danger')
            ->emit('undoEditingPost', [$post->id])
            ->close(), // [tl! focus]
    ]);
```
