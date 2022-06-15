@props([
    'actions',
    'color' => null,
    'darkMode' => false,
    'icon' => 'heroicon-o-dots-vertical',
    'label' => __('filament-support::actions/group.trigger.label'),
    'tooltip' => null,
])

<div x-data="{ isOpen: false }" {{ $attributes->class(['relative']) }}>
    <x-filament-support::icon-button
        x-on:click="isOpen = ! isOpen"
        :color="$color"
        :dark-mode="$darkMode"
        :icon="$icon"
        :tooltip="$tooltip"
        class="-my-2"
    >
        <x-slot name="label">
            {{ $label }}
        </x-slot>
    </x-filament-support::icon-button>

    <div
        x-show="isOpen"
        x-on:click.away="isOpen = false"
        x-transition
        x-cloak
        @class([
            'absolute right-0 rtl:left-0 rtl:right-auto z-20 mt-4 shadow-xl ring-1 ring-gray-900/10 overflow-hidden rounded-xl w-52 filament-action-group-dropdown',
            'dark:ring-white/20' => $darkMode,
        ])
    >
        <ul @class([
            'py-1 space-y-1 bg-white shadow rounded-xl',
            'dark:bg-gray-700 dark:divide-gray-600' => $darkMode,
        ])>
            @foreach ($actions as $action)
                @if (! $action->isHidden())
                    {{ $action }}
                @endif
            @endforeach
        </ul>
    </div>
</div>
