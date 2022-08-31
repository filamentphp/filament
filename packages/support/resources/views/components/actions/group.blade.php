@props([
    'actions',
    'color' => null,
    'darkMode' => false,
    'icon' => 'heroicon-o-dots-vertical',
    'label' => __('filament-support::actions/group.trigger.label'),
    'tooltip' => null,
])

<x-filament-support::dropdown
    :dark-mode="$darkMode"
    placement="bottom-end"
    teleport
    {{ $attributes }}
>
    <x-slot name="trigger">
        <x-filament-support::icon-button
            :color="$color"
            :dark-mode="$darkMode"
            :icon="$icon"
            :tooltip="$tooltip"
        >
            <x-slot name="label">
                {{ $label }}
            </x-slot>
        </x-filament-support::icon-button>
    </x-slot>

    @foreach ($actions as $action)
        @if (! $action->isHidden())
            {{ $action }}
        @endif
    @endforeach
</x-filament-support::dropdown>
