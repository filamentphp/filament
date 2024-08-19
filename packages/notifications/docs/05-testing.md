---
title: Testing
---

## Overview

All examples in this guide will be written using [Pest](https://pestphp.com). To use Pest's Livewire plugin for testing, you can follow the installation instructions in the Pest documentation on plugins: [Livewire plugin for Pest](https://pestphp.com/docs/plugins#livewire). However, you can easily adapt this to PHPUnit.

## Testing session notifications

To check if a notification was sent using the session, use the `assertNotified()` helper:

```php
use function Pest\Livewire\livewire;

it('sends a notification', function () {
    livewire(CreatePost::class)
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
use function Pest\Livewire\livewire;

it('sends a notification', function () {
    livewire(CreatePost::class)
        ->assertNotified('Unable to create post');
});
```

Or test if the exact notification was sent:

```php
use Filament\Notifications\Notification;
use function Pest\Livewire\livewire;

it('sends a notification', function () {
    livewire(CreatePost::class)
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
use function Pest\Livewire\livewire;

it('does not send a notification', function () {
    livewire(CreatePost::class)
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
