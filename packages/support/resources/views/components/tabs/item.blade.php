@props([
    'active' => false,
    'alpineActive' => null,
    'badge' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconPosition' => 'before',
    'tag' => 'button',
    'type' => 'button',
])

@php
    $inactiveItemClasses = 'text-gray-700 dark:text-gray-300';

    $activeItemClasses = 'fi-tabs-item-active bg-gray-950/5 text-primary-600 dark:bg-white/5 dark:text-primary-400';

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-tabs-item-icon h-5 w-5',
        match ($iconColor) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($iconColor, shades: [500]) => $iconColor !== 'gray',
    ]);
@endphp

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @endif
    @if ($alpineActive)
        x-bind:class="{
            @js($inactiveItemClasses): ! {{ $alpineActive }},
            @js($activeItemClasses): {{ $alpineActive }},
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-selected' => $active,
                'role' => 'tab',
            ])
            ->class([
                'fi-tabs-item flex items-center gap-x-2 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5',
                $inactiveItemClasses => (! $alpineActive) && (! $active),
                $activeItemClasses => (! $alpineActive) && $active,
            ])
    }}
>
    @if ($icon && $iconPosition === 'before')
        <x-filament::icon
            :icon="$icon"
            :style="$iconStyles"
            :class="$iconClasses"
            :style="$iconStyles"
        />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if ($icon && $iconPosition === 'after')
        <x-filament::icon
            :icon="$icon"
            :class="$iconClasses"
            :style="$iconStyles"
        />
    @endif

    @if ($badge)
        <x-filament::badge size="sm">
            {{ $badge }}
        </x-filament::badge>
    @endif
</{{ $tag }}>
