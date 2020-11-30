@props([
    'submit',
])

<form wire:submit.prevent="{{ $submit }}" {{ $attributes->merge(['class' => 'space-y-4']) }}>
    {{ $slot }}
</form>