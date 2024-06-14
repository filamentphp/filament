@props([
    'availableHeight' => null,
    'availableWidth' => null,
    'maxHeight' => null,
    'offset' => 8,
    'placement' => null,
    'shift' => false,
    'size' => false,
    'sizePadding' => 16,
    'teleport' => false,
    'trigger' => null,
    'width' => null,
])

@php
    use Filament\Support\Enums\MaxWidth;

    $sizeConfig = collect([
        'availableHeight' => $availableHeight,
        'availableWidth' => $availableWidth,
        'padding' => $sizePadding,
    ])->filter()->toJson();
@endphp

<div
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
    {{ $attributes->class(['fi-dropdown']) }}
>
    <div
        x-on:click="toggle"
        {{ $trigger->attributes->class(['fi-dropdown-trigger flex cursor-pointer']) }}
    >
        {{ $trigger }}
    </div>

    @if (! \Filament\Support\is_slot_empty($slot))
        <div
            x-cloak
            x-float{{ $placement ? ".placement.{$placement}" : '' }}{{ $size ? '.size' : '' }}.flip{{ $shift ? '.shift' : '' }}{{ $teleport ? '.teleport' : '' }}{{ $offset ? '.offset' : '' }}="{ offset: {{ $offset }}, {{ $size ? ('size: ' . $sizeConfig) : '' }} }"
            x-ref="panel"
            x-transition:enter-start="opacity-0"
            x-transition:leave-end="opacity-0"
            @if ($attributes->has('wire:key'))
                wire:ignore.self
                wire:key="{{ $attributes->get('wire:key') }}.panel"
            @endif
            @class([
                'fi-dropdown-panel absolute z-10 w-screen divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10',
                match ($width) {
                    // These max width classes need to be `!important` otherwise they will be usurped by the Floating UI "size" middleware.
                    MaxWidth::ExtraSmall, 'xs' => '!max-w-xs',
                    MaxWidth::Small, 'sm' => '!max-w-sm',
                    MaxWidth::Medium, 'md' => '!max-w-md',
                    MaxWidth::Large, 'lg' => '!max-w-lg',
                    MaxWidth::ExtraLarge, 'xl' => '!max-w-xl',
                    MaxWidth::TwoExtraLarge, '2xl' => '!max-w-2xl',
                    MaxWidth::ThreeExtraLarge, '3xl' => '!max-w-3xl',
                    MaxWidth::FourExtraLarge, '4xl' => '!max-w-4xl',
                    MaxWidth::FiveExtraLarge, '5xl' => '!max-w-5xl',
                    MaxWidth::SixExtraLarge, '6xl' => '!max-w-6xl',
                    MaxWidth::SevenExtraLarge, '7xl' => '!max-w-7xl',
                    null => '!max-w-[14rem]',
                    default => $width,
                },
                'overflow-y-auto' => $maxHeight || $size,
            ])
            @style([
                "max-height: {$maxHeight}" => $maxHeight,
            ])
        >
            {{ $slot }}
        </div>
    @endif
</div>
