---
title: Sending notifications
---

## Overview

> To start, make sure the package is [installed](installation) - `@livewire('notifications')` should be in your Blade layout somewhere.

Notifications are sent using a `Notification` object that's constructed through a fluent API. Calling the `send()` method on the `Notification` object will dispatch the notification and display it in your application. As the session is used to flash notifications, they can be sent from anywhere in your code, including JavaScript, not just Livewire components.

```php
<?php

namespace App\Http\Livewire;

use Filament\Notifications\Notification;
use Livewire\Component;

class EditPost extends Component
{
    public function save(): void
    {
        // ...

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
```

![Notification](https://user-images.githubusercontent.com/44533235/180995786-c9d6ac68-959a-45d2-8f05-e09ff2b9abd9.png)

## Setting a title

The main message of the notification is shown in the title. You can set the title as follows:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .send()
```

Markdown text will automatically be rendered if passed to the title.

## Setting an icon

Optionally, a notification can have an icon that's displayed in front of its content. You may also set a color for the icon, which defaults to the `secondary` color specified in your `tailwind.config.js` file. The icon can be the name of any Blade component. By default, the [Blade Heroicons v1](https://github.com/blade-ui-kit/blade-heroicons/tree/1.3.1) package is installed, so you may use the name of any [Heroicons v1](https://v1.heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->icon('heroicon-o-document-text')
    ->iconColor('success')
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .icon('heroicon-o-document-text')
    .iconColor('success')
    .send()
```

![Notification with icon](https://user-images.githubusercontent.com/44533235/180996863-1eee77fb-2504-4d70-972d-d120bef631dc.png)

Notifications often have a status like `success`, `warning` or `danger`. Instead of manually setting the corresponding icons and colors, there's a `status()` method which you can pass the status. You may also use the dedicated `success()`, `warning()` and `danger()` methods instead. So, cleaning up the above example would look like this:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .send()
```

![Success, warning and danger notifications](https://user-images.githubusercontent.com/44533235/180995801-3e706ca6-773b-47a0-9fc6-3e28900a9ea9.png)

## Setting a background color

Notifications have no background color by default. You may want to provide additional context to your notification by setting a color as follows:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->color('success') // [tl! focus]
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .color('success') // [tl! focus]
    .send()
```

## Setting a duration

By default, notifications are shown for 6 seconds before they're automatically closed. You may specify a custom duration value in milliseconds as follows:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->duration(5000)
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .duration(5000)
    .send()
```

If you prefer setting a duration in seconds instead of milliseconds, you can do so:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->seconds(5)
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .seconds(5)
    .send()
```

You might want some notifications to not automatically close and require the user to close them manually. This can be achieved by making the notification persistent:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->persistent()
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .persistent()
    .send()
```

## Setting body text

Additional notification text can be shown in the body. Similar to the title, it supports Markdown:

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .body('Changes to the **post** have been saved.')
    .send()
```

![Notification with Markdown body](https://user-images.githubusercontent.com/44533235/180995813-ce93e747-0f66-4fc5-becb-7e535fb80e46.png)

## Adding notification actions

Notifications support [actions](../actions/trigger-button), which are buttons that render below the content of the notification. They can open a URL or emit a Livewire event. Actions can be defined as follows:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->body('Changes to the **post** have been saved.')
    ->actions([
        Action::make('view')
            ->button(),
        Action::make('undo')
            ->color('gray'),
    ])
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .body('Changes to the **post** have been saved.')
    .actions([
        new NotificationAction('view')
            .button(),
        new NotificationAction('undo')
            .color('gray'),
    ])
    .send()
```

![Notification with actions](https://user-images.githubusercontent.com/44533235/180995819-ed5c78fa-b567-4bc6-9e5c-64fe615c4360.png)

You can learn more about how to style action buttons [here](../actions/trigger-button).

### Opening URLs from notification actions

You can open a URL, optionally in a new tab, when clicking on an action:

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
            ->url(route('posts.show', $post), shouldOpenInNewTab: true)
        Action::make('undo')
            ->color('gray'),
    ])
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .body('Changes to the **post** have been saved.')
    .actions([
        new NotificationAction('view')
            .button()
            .url('/view')
            .openUrlInNewTab(),
        new NotificationAction('undo')
            .color('gray'),
    ])
    .send()
```

### Emitting Livewire events from notification actions

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
            ->color('gray')
            ->emit('undoEditingPost', [$post->id]),
    ])
    ->send();
```

You can also `emitSelf`, `emitUp` and `emitTo`:

```php
Action::make('undo')
    ->color('secondary')
    ->emitSelf('undoEditingPost', [$post->id])

Action::make('undo')
    ->color('secondary')
    ->emitUp('undoEditingPost', [$post->id])

Action::make('undo')
    ->color('secondary')
    ->emitTo('another_component', 'undoEditingPost', [$post->id])
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .body('Changes to the **post** have been saved.')
    .actions([
        new NotificationAction('view')
            .button()
            .url('/view')
            .openUrlInNewTab(),
        new NotificationAction('undo')
            .color('gray')
            .emit('undoEditingPost'),
    ])
    .send()
```

Similarly, `emitSelf`, `emitUp` and `emitTo` are also available:

```js
new NotificationAction('undo')
    .color('secondary')
    .emitSelf('undoEditingPost')

new NotificationAction('undo')
    .color('secondary')
    .emitUp('undoEditingPost')

new NotificationAction('undo')
    .color('secondary')
    .emitTo('another_component', 'undoEditingPost')
```

### Closing notifications from actions

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
            ->color('gray')
            ->emit('undoEditingPost', [$post->id])
            ->close(),
    ])
    ->send();
```

Or with JavaScript:

```js
new Notification()
    .title('Saved successfully')
    .success()
    .body('Changes to the **post** have been saved.')
    .actions([
        new NotificationAction('view')
            .button()
            .url('/view')
            .openUrlInNewTab(),
        new NotificationAction('undo')
            .color('gray')
            .emit('undoEditingPost')
            .close(),
    ])
    .send()
```

## Using the JavaScript objects

The JavaScript objects (`Notification` and `NotificationAction`) are assigned to `window.Notification` and `window.NotificationAction`, so they are available in on-page scripts.

You may also import them in a bundled JavaScript file:

```js
import { Notification, NotificationAction } from '../../vendor/filament/notifications/dist/index.js'

// ...
```
