@props([
    'actions',
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-bulk-actions']) }}
    placement="bottom-start"
>
    <x-slot name="trigger">
        <x-tables::bulk-actions.trigger />
    </x-slot>

    <x-tables::dropdown.list>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </x-tables::dropdown.list>
</x-tables::dropdown>
