---
title: Advanced actions
---

## Action utility injection

The vast majority of methods used to configure actions accept functions as parameters instead of hardcoded values:

```php
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
