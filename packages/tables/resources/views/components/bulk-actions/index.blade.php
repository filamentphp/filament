@props([
    'actions',
])

<x-filament-tables::dropdown
    {{ $attributes->class(['filament-tables-bulk-actions']) }}
    placement="bottom-start"
>
    <x-slot name="trigger">
        <x-filament-tables::bulk-actions.trigger />
    </x-slot>

    <x-filament-tables::dropdown.list>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </x-filament-tables::dropdown.list>
</x-filament-tables::dropdown>
