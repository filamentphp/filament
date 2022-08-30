@props([
    'darkMode' => false,
    'placement' => null,
    'shift' => false,
    'teleport' => false,
    'trigger' => null,
    'width' => null,
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
        class="filament-dropdown-trigger cursor-pointer"
        x-on:click="toggle"
    >
        {{ $trigger }}
    </div>

    <div
        x-ref="panel"
        x-float{{ $placement ? ".placement.{$placement}" : '' }}.flip.offset{{ $shift ? '.shift' : '' }}{{ $teleport ? '.teleport' : '' }}="{ offset: 8 }"
        x-cloak
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave-end="opacity-0 scale-95"
        @if ($attributes->has('wire:key'))
            wire:ignore.self
            wire:key="{{ $attributes->get('wire:key') }}.panel"
        @endif
        @class([
            'filament-dropdown-panel absolute z-10 w-full rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition',
            'dark:bg-gray-800 dark:ring-white/10' => $darkMode,
            match ($width) {
                'xs' => 'max-w-xs',
                'sm' => 'max-w-sm',
                'md' => 'max-w-md',
                'lg' => 'max-w-lg',
                'xl' => 'max-w-xl',
                '2xl' => 'max-w-2xl',
                '3xl' => 'max-w-3xl',
                '4xl' => 'max-w-4xl',
                '5xl' => 'max-w-5xl',
                '6xl' => 'max-w-6xl',
                '7xl' => 'max-w-7xl',
                default => 'max-w-[14rem]',
            },
        ])
    >
        {{ $slot }}
    </div>
</div>
