---
title: Advanced actions
---

## Action utility injection

The vast majority of methods used to configure actions accept functions as parameters instead of hardcoded values:

```php
use Filament\Actions\Action;

Action::make('edit')
    ->label('Edit post')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the current modal form data

If you wish to access the current [modal form data](modals#modal-forms), define a `$data` parameter:

```php
function (array $data) {
    // ...
}
```

Be aware that this will be empty if the modal has not been submitted yet.

### Injecting the current arguments

If you wish to access the [current arguments](adding-an-action-to-a-livewire-component#passing-action-arguments) that have been passed to the action, define an `$arguments` parameter:

```php
function (array $arguments) {
    // ...
}
```

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance that the action belongs to, define a `$livewire` parameter:

```php
use Livewire\Component;

function (Component $livewire) {
    // ...
}
```

### Injecting the current action instance

If you wish to access the current action instance, define a `$action` parameter:

```php
function (Action $action) {
    // ...
}
```

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you are able to combine multiple parameters in any order:

```php
use Livewire\Component;

function (array $arguments, Component $livewire) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use Illuminate\Http\Request;

function (Request $request, array $arguments) {
    // ...
}
```

## Rate limiting actions

You can rate limit actions by using the `rateLimit()` method. This method accepts the number of attempts per minute that a user IP address can make. If the user exceeds this limit, the action will not run and a notification will be shown:

```php
use Filament\Actions\Action;

Action::make('delete')
    ->rateLimit(5)
```

If the action opens a modal, the rate limit will be applied when the modal is submitted.

If an action is opened with arguments or for a specific Eloquent record, the rate limit will apply to each unique combination of arguments or record for each action. The rate limit is also unique to the current Livewire component / page in a panel.

## Customizing the rate limited notification

When an action is rate limited, a notification is dispatched to the user, which indicates the rate limit.

To customize the title of this notification, use the `rateLimitedNotificationTitle()` method:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->rateLimit(5)
    ->rateLimitedNotificationTitle('Slow down!')
```

You may customize the entire notification using the `rateLimitedNotification()` method:

```php
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

DeleteAction::make()
    ->rateLimit(5)
    ->rateLimitedNotification(
       fn (TooManyRequestsException $exception): Notification => Notification::make()
            ->warning()
            ->title('Slow down!')
            ->body("You can try deleting again in {$exception->secondsUntilAvailable} seconds."),
    )
```

### Customizing the rate limit behaviour

If you wish to customize the rate limit behaviour, you can use Laravel's [rate limiting](https://laravel.com/docs/rate-limiting#basic-usage) features and Filament's [flash notifications](../notifications/sending-notifications) together in the action.

If you want to rate limit immediately when an action modal is opened, you can do so in the `mountUsing()` method:

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

Action::make('delete')
    ->mountUsing(function () {
        if (RateLimiter::tooManyAttempts(
            $rateLimitKey = 'delete:' . auth()->id(),
            maxAttempts: 5,
        )) {
            Notification::make()
                ->title('Too many attempts')
                ->body('Please try again in ' . RateLimiter::availableIn($rateLimitKey) . ' seconds.')
                ->danger()
                ->send();
                
            return;
        }
        
         RateLimiter::hit($rateLimitKey);
    })
```

If you want to rate limit when an action is run, you can do so in the `action()` method:

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

Action::make('delete')
    ->action(function () {
        if (RateLimiter::tooManyAttempts(
            $rateLimitKey = 'delete:' . auth()->id(),
            maxAttempts: 5,
        )) {
            Notification::make()
                ->title('Too many attempts')
                ->body('Please try again in ' . RateLimiter::availableIn($rateLimitKey) . ' seconds.')
                ->danger()
                ->send();
                
            return;
        }
        
         RateLimiter::hit($rateLimitKey);
        
        // ...
    })
```
