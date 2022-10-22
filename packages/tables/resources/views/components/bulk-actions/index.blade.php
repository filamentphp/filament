@props([
    'actions',
])

<x-filament::dropdown
    {{ $attributes->class(['filament-tables-bulk-actions']) }}
    placement="bottom-start"
>
    <x-slot name="trigger">
        <x-filament-tables::bulk-actions.trigger />
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
