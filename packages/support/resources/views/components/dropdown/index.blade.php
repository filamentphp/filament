@props([
    'darkMode' => false,
    'placement' => null,
    'teleport' => false,
    'trigger' => null,
    'width' => 'sm',
])

<div
    {{ $attributes->class(['filament-dropdown']) }}
    x-data="{
        toggle: function (event) {
            $refs.panel.toggle(event)
        },
        open: function (event) {
            $refs.panel.open(event)
        },
        close: function (event) {
            $refs.panel.close(event)
        },
    }"
>
    <div
        class="filament-dropdown-trigger"
        x-on:click="toggle"
    >
        {{ $trigger }}
    </div>

    <div
        x-ref="panel"
        x-float{{ $placement ? ".placement.$placement" : '' }}.flip.offset{{ $teleport ? '.teleport' : '' }}="{ offset: 8 }"
        x-cloak
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
        @if ($attributes->has('wire:key'))
            wire:ignore.self
            wire:key="{{ $attributes->get('wire:key') }}.panel"
        @endif
        @class([
            'filament-dropdown-panel absolute z-10 w-full max-w-[224px] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-md transition',
            'dark:border-gray-700 dark:bg-gray-800' => $darkMode,
            // match ($width) {
            //     'xs' => 'max-w-xs',
            //     'md' => 'max-w-md',
            //     'lg' => 'max-w-lg',
            //     'xl' => 'max-w-xl',
            //     '2xl' => 'max-w-2xl',
            //     '3xl' => 'max-w-3xl',
            //     '4xl' => 'max-w-4xl',
            //     '5xl' => 'max-w-5xl',
            //     '6xl' => 'max-w-6xl',
            //     '7xl' => 'max-w-7xl',
            //     default => 'max-w-sm',
            // },
        ])
    >
        {{ $slot }}
    </div>
</div>
