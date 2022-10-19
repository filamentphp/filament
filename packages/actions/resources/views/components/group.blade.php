@props([
    'actions',
    'color' => null,
    'darkMode' => false,
    'dropdownPlacement' => null,
    'icon' => 'heroicon-m-ellipsis-vertical',
    'label' => __('filament-actions::group.trigger.label'),
    'size' => null,
    'tooltip' => null,
])

<x-filament-actions::dropdown
    :dark-mode="$darkMode"
    :placement="$dropdownPlacement ?? 'bottom-end'"
    teleport
    {{ $attributes }}
>
    <x-slot name="trigger">
        <x-filament-actions::icon-button
            :color="$color"
            :dark-mode="$darkMode"
            :icon="$icon"
            :size="$size"
            :tooltip="$tooltip"
        >
            <x-slot name="label">
                {{ $label }}
            </x-slot>
        </x-filament-actions::icon-button>
    </x-slot>

    <x-filament-actions::dropdown.list>
        @foreach ($actions as $action)
            @if (! $action->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </x-filament-actions::dropdown.list>
</x-filament-actions::dropdown>
