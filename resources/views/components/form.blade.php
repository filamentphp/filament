@props([
    'submit' => 'submit',
])

<form wire:submit.prevent="{{ $submit }}" {{ $attributes->merge(['class' => 'space-y-6']) }}>
    {{ $slot }}
</form>