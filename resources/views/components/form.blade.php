@props([
    'submit' => 'submit',
])

<form wire:submit.prevent="{{ $submit }}" {{ $attributes }}>
    {{ $slot }}
</form>