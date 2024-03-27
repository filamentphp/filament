---
title: Testing
---

## Overview

All examples in this guide will be written using [Pest](https://pestphp.com). However, you can easily adapt this to PHPUnit.

## Testing session notifications

To check if a notification was sent using the session, use the `assertNotified()` helper:

```php
use Livewire\Livewire;

it('sends a notification', function () {
    Livewire::test(CreatePost::class)
        ->assertNotified();
});
```

```php
use Filament\Notifications\Notification;

it('sends a notification', function () {
    Notification::assertNotified();
});
```

```php
use function Filament\Notifications\Testing\assertNotified;

it('sends a notification', function () {
    assertNotified();
});
```

You may optionally pass a notification title to test for:

```php
use Filament\Notifications\Notification;
use Livewire\Livewire;

it('sends a notification', function () {
    Livewire::test(CreatePost::class)
        ->assertNotified('Unable to create post');
});
```

Or test if the exact notification was sent:

```php
use Filament\Notifications\Notification;
use Livewire\Livewire;

it('sends a notification', function () {
    Livewire::test(CreatePost::class)
        ->assertNotified(
            Notification::make()
                ->danger()
                ->title('Unable to create post')
                ->body('Something went wrong.'),
        );
});
```

Conversely, you can assert that a notification was not sent:

```php
use Filament\Notifications\Notification;
use Livewire\Livewire;

it('does not send a notification', function () {
    Livewire::test(CreatePost::class)
        ->assertNotNotified()
        // or
        ->assertNotNotified('Unable to create post')
        // or
        ->assertNotified(
            Notification::make()
                ->danger()
                ->title('Unable to create post')
                ->body('Something went wrong.'),
        );
```
