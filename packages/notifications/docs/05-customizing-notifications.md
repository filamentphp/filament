---
title: Customizing notifications
---

## Overview

Notifications come fully styled out of the box. However, if you want to apply your own styling or use a custom view to render notifications, there are multiple options.

## Styling notifications

Notifications have dedicated CSS classes you can hook into to apply your own styling. Open the inspector in your browser to find out which classes you need to target.

## Positioning notifications

You can configure the alignment of the notifications in a service provider or middleware, by calling `Notifications::alignment()` and `Notifications::verticalAlignment()`. You can pass `Alignment::Start`, `Alignment::Center`, `Alignment::End`, `VerticalAlignment::Start`, `VerticalAlignment::Center` or `VerticalAlignment::End`:

```php
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;

Notifications::alignment(Alignment::Start);
Notifications::verticalAlignment(VerticalAlignment::End);
```

## Using a custom notification view

If your desired customization can't be achieved using the CSS classes above, you can create a custom view to render the notification. To configure the notification view, call the static `configureUsing()` method inside a service provider's `boot()` method and specify the view to use:

```php
use Filament\Notifications\Notification;

Notification::configureUsing(function (Notification $notification): void {
    $notification->view('filament.notifications.notification');
});
```

Next, create the view, in this example `resources/views/filament/notifications/notification.blade.php`. The view should use the package's base notification component for the notification functionality and pass the available `$notification` variable through the `notification` attribute. This is the bare minimum required to create your own notification view:

```blade
<x-filament-notifications::notification :notification="$notification">
    {{-- Notification content --}}
</x-filament-notifications::notification>
```

Getters for all notification properties will be available in the view. So, a custom notification view might look like this:

```blade
<x-filament-notifications::notification
    :notification="$notification"
    class="flex w-80 rounded-lg transition duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:leave-end="opacity-0"
>
    <h4>
        {{ $getTitle() }}
    </h4>

    <p>
        {{ $getDate() }}
    </p>

    <p>
        {{ $getBody() }}
    </p>

    <span x-on:click="close">
        Close
    </span>
</x-filament-notifications::notification>
```

## Using a custom notification object

Maybe your notifications require additional functionality that's not defined in the package's `Notification` class. Then you can create your own `Notification` class, which extends the package's `Notification` class. For example, your notification design might need a size property.

Your custom `Notification` class in `app/Notifications/Notification.php` might contain:

```php
<?php

namespace App\Notifications;

use Filament\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    protected string $size = 'md';

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'size' => $this->getSize(),
        ];
    }

    public static function fromArray(array $data): static
    {
        return parent::fromArray($data)->size($data['size']);
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }
}
```

Next, you should bind your custom `Notification` class into the container inside a service provider's `register()` method:

```php
use App\Notifications\Notification;
use Filament\Notifications\Notification as BaseNotification;

$this->app->bind(BaseNotification::class, Notification::class);
```

You can now use your custom `Notification` class in the same way as you would with the default `Notification` object.
