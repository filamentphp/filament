---
title: Sending notifications
---

> To start, make sure the package is [installed](installation) - `@livewire('notifications')` should be in your Blade layout somewhere.

Notifications are sent using a `Notification` object that's constructed through a fluent API. Calling the `send()` method on the `Notification` object will dispatch the notification and display it in your application. As the session is used to flash notifications, they can be sent from anywhere in your code, not just Livewire components.

```php
<?php

namespace App\Http\Livewire;

use Filament\Notifications\Notification; // [tl! focus]
use Livewire\Component;

class EditPost extends Component
{
    public function save(): void
    {
        // ...
    
        Notification::make() // [tl! focus:start]
            ->title('Saved successfully')
            ->success()
            ->send(); // [tl! focus:end]
    }
}
```

![Notification](https://user-images.githubusercontent.com/44533235/180995786-c9d6ac68-959a-45d2-8f05-e09ff2b9abd9.png)

## Title

The main message of the notification is shown in the title. You can set the title as follows:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully') // [tl! focus]
    ->send();
```

Markdown text will automatically be rendered if passed to the title.

## Icon

Optionally, a notification can have an icon that's displayed in front of its content. You may also set a color for the icon, which defaults to the `secondary` color specified in your `tailwind.config.js` file. The icon can be the name of any Blade component. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->icon('heroicon-o-document-text') // [tl! focus:start]
    ->iconColor('success') // [tl! focus:end]
    ->send();
```

![Notification with icon](https://user-images.githubusercontent.com/44533235/180996863-1eee77fb-2504-4d70-972d-d120bef631dc.png)

Notifications often have a status like `success`, `warning` or `danger`. Instead of manually setting the corresponding icons and colors, there's a `status()` method which you can pass the status. You may also use the dedicated `success()`, `warning()` and `danger()` methods instead. So, cleaning up the above example would look like this:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success() // [tl! focus]
    ->send();
```

![Success, warning and danger notifications](https://user-images.githubusercontent.com/44533235/180995801-3e706ca6-773b-47a0-9fc6-3e28900a9ea9.png)

## Duration

By default, notifications are shown for 6 seconds before they're automatically closed. You may specify a custom duration value in milliseconds as follows:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->duration(5000) // [tl! focus]
    ->send();
```

If you prefer setting a duration in seconds instead of milliseconds, you can do so:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->seconds(5) // [tl! focus]
    ->send();
```

You might want some notifications to not automatically close and require the user to close them manually. This can be achieved by making the notification persistent:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->persistent() // [tl! focus]
    ->send();
```

## Body

Additional notification text can be shown in the body. Similar to the title, it supports Markdown:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.') // [tl! focus]
    ->send();
```

![Notification with Markdown body](https://user-images.githubusercontent.com/44533235/180995813-ce93e747-0f66-4fc5-becb-7e535fb80e46.png)

## Actions

Notifications support actions that render a button or link which may open a URL or emit a Livewire event. Actions will render as link by default, but you may configure it to render a button using the `button()` method. Actions can be defined as follows:

```php
use Filament\Notifications\Actions\Action; // [tl! focus]
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([ // [tl! focus:start]
        Action::make('view')
            ->button(),
        Action::make('undo')
            ->color('secondary'),
    ]) // [tl! focus:end]
    ->send();
```

![Notification with actions](https://user-images.githubusercontent.com/44533235/180995819-ed5c78fa-b567-4bc6-9e5c-64fe615c4360.png)

### Opening URLs

If clicking on an action should open a URL, optionally in a new tab, you can do so:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('view')
            ->button()
            ->url(route('posts.show', $post), shouldOpenInNewTab: true) // [tl! focus]
        Action::make('undo')
            ->color('secondary'),
    ])
    ->send();
```

### Emitting events

Sometimes you want to execute additional code when a notification action is clicked. This can be achieved by setting a Livewire event which should be emitted on clicking the action. You may optionally pass an array of data, which will be available as parameters in the event listener on your Livewire component:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('view')
            ->button()
            ->url(route('posts.show', $post), shouldOpenInNewTab: true),
        Action::make('undo')
            ->color('secondary')
            ->emit('undoEditingPost', [$post->id]), // [tl! focus]
    ])
    ->send();
```

### Closing notifications

After opening a URL or emitting an event from your action, you may want to close the notification right away:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('view')
            ->button()
            ->url(route('posts.show', $post), shouldOpenInNewTab: true),
        Action::make('undo')
            ->color('secondary')
            ->emit('undoEditingPost', [$post->id])
            ->close(), // [tl! focus]
    ])
    ->send();
```
