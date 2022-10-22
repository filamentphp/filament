@props([
    'actions',
    'color' => null,
    'dropdownPlacement' => null,
    'icon' => 'heroicon-m-ellipsis-vertical',
    'label' => __('filament-actions::group.trigger.label'),
    'size' => null,
    'tooltip' => null,
])

<x-filament::dropdown
    :placement="$dropdownPlacement ?? 'bottom-end'"
    teleport
    {{ $attributes }}
>
    <x-slot name="trigger">
        <x-filament::icon-button
            :color="$color"
            :icon="$icon"
            :size="$size"
            :tooltip="$tooltip"
        >
            <x-slot name="label">
                {{ $label }}
            </x-slot>
        </x-filament::icon-button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($actions as $action)
            @if (! $action->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
