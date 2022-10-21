@props([
    'actions',
])

<x-filament-support::dropdown
    {{ $attributes->class(['filament-tables-bulk-actions']) }}
    placement="bottom-start"
>
    <x-slot name="trigger">
        <x-filament-tables::bulk-actions.trigger />
    </x-slot>

    <x-filament-support::dropdown.list>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </x-filament-support::dropdown.list>
</x-filament-support::dropdown>
