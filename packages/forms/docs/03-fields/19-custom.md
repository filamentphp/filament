---
title: Custom fields
---

## View fields

Aside from [building custom fields](custom), you may create "view" fields which allow you to create custom fields without extra PHP classes.

```php
use Filament\Forms\Components\ViewField;

ViewField::make('notifications')->view('filament.forms.components.range-slider')
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` closure may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js.

Using [Livewire's entangle](https://laravel-livewire.com/docs/alpine-js#sharing-state) allows sharing state with Alpine.js:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
    </div>
</x-dynamic-component>
```

Or, you may bind the value to a Livewire property using [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding):

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <input wire:model.defer="{{ $getStatePath() }}" />
</x-dynamic-component>
```

## Custom field classes

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom field to use once, you could instead use a [view field](#view) to render any custom Blade file.

To create a custom field class and view, you may use the following command:

```bash
php artisan make:form-field RangeSlider
```

This will create the following field class:

```php
use Filament\Forms\Components\Field;

class RangeSlider extends Field
{
    protected string $view = 'filament.forms.components.range-slider';
}
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` closure may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
    </div>
</x-dynamic-component>
```
