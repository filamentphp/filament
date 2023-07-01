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
    $iconColorClasses = \Illuminate\Support\Arr::toCssClasses([
        'text-custom-600 dark:text-custom-400' => $active,
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($iconColor, shades: [400, 600]) => $iconColorClasses,
    ]);
@endphp

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @endif
    @if ($alpineActive)
        x-bind:class="{
            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400':
                ! {{ $alpineActive }},
            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600':
                {{ $alpineActive }},
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-selected' => $active,
                'role' => 'tab',
            ])
            ->class([
                'filament-tabs-item flex h-8 items-center gap-3 whitespace-nowrap rounded-md px-5 font-medium outline-none focus:ring-2 focus:ring-inset focus:ring-primary-600',
                'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400' => (! $active) && (! $alpineActive),
                'bg-white text-primary-600 shadow dark:bg-primary-600 dark:text-white' => $active && (! $alpineActive),
            ])
    }}
>
    @if ($icon && $iconPosition === 'before')
        <x-filament::icon
            :name="$icon"
            :color="$iconColorClasses"
            alias="support::tabs.item"
            size="h-5 w-5"
            :style="$iconStyles"
            x-bind:class="{
                '{{ $iconColorClasses }}': ! ({{ $alpineActive }}),
            }"
        />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if ($icon && $iconPosition === 'after')
        <x-filament::icon
            :name="$icon"
            :color="$iconColorClasses"
            alias="support::tabs.item"
            size="h-5 w-5"
            :style="$iconStyles"
            x-bind:class="{
                '{{ $iconColorClasses }}': ! ({{ $alpineActive }}),
            }"
        />
    @endif

    @if ($badge)
        <span
            @if ($alpineActive)
                x-bind:class="{
                    'bg-white dark:bg-gray-600': ! {{ $alpineActive }},
                    'bg-primary-600 text-white font-medium dark:bg-white dark:text-primary-600':
                        {{ $alpineActive }},
                }"
            @endif
            @class([
                'min-h-4 inline-flex items-center justify-center whitespace-normal rounded-xl px-2 py-0.5 text-xs font-medium tracking-tight',
                'bg-white dark:bg-gray-600' => (! $active) && (! $alpineActive),
                'bg-primary-600 font-medium text-white dark:bg-white dark:text-primary-600' => $active && (! $alpineActive),
            ])
        >
            {{ $badge }}
        </span>
    @endif
</{{ $tag }}>
