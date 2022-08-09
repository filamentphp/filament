@props([
    'darkMode' => false,
    'placement' => 'bottom-end',
    'teleport' => false,
    'trigger' => null,
    'width' => 'sm',
])

<div
    x-data
    {{ $attributes->class('filament-dropdown') }}
>
    <div
        class="filament-dropdown-trigger"
        x-on:click="$refs.panel.toggle"
    >
        {{ $trigger }}
    </div>

    <div
        x-ref="panel"
        x-cloak
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
        x-float.placement.{{ $placement }}.flip.offset{{ $teleport ? '.teleport' : '' }}="{ offset: 8 }"
        @class([
            'filament-dropdown-panel absolute z-10 w-max overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md transition',
            'dark:border-gray-700 dark:bg-gray-800' => $darkMode,
            match ($width) {
                'xs' => 'max-w-xs',
                'md' => 'max-w-md',
                'lg' => 'max-w-lg',
                'xl' => 'max-w-xl',
                '2xl' => 'max-w-2xl',
                '3xl' => 'max-w-3xl',
                '4xl' => 'max-w-4xl',
                '5xl' => 'max-w-5xl',
                '6xl' => 'max-w-6xl',
                '7xl' => 'max-w-7xl',
                default => 'max-w-sm',
            },
        ])
    >
        {{ $slot }}
    </div>
</div>
