@props([
    'active' => false,
    'alpineActive' => null,
    'badge' => null,
    'icon' => null,
    'iconColor' => null,
    'iconPosition' => 'before',
    'tag' => 'button',
    'type' => 'button',
])

@php
    $iconColorClasses = $active ? null : match ($iconColor) {
        'danger' => 'text-danger-600 dark:text-danger-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
        'info' => 'text-info-600 dark:text-info-400',
        'primary' => 'text-primary-600 dark:text-primary-400',
        'secondary' => 'text-secondary-600 dark:text-secondary-400',
        'success' => 'text-success-600 dark:text-success-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        default => $iconColor,
    };
@endphp

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @endif
    @if ($alpineActive)
        x-bind:class="{
            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400': ! ({{ $alpineActive }}),
            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600': {{ $alpineActive }},
        }"
    @endif
    {{ $attributes
        ->merge([
            'aria-selected' => $active,
            'role' => 'tab',
        ])
        ->class([
            'filament-tabs-item flex whitespace-nowrap items-center gap-3 h-8 px-5 font-medium rounded-md outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset',
            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400' => (! $active) && (! $alpineActive),
            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600' => $active,
        ]) }}
>
    @if ($icon && $iconPosition === 'before')
        <x-filament::icon
            :name="$icon"
            :color="$iconColorClasses"
            alias="support::tabs.item"
            size="h-5 w-5"
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
            x-bind:class="{
                '{{ $iconColorClasses }}': ! ({{ $alpineActive }}),
            }"
        />
    @endif

    @if ($badge)
        <span
            @if ($alpineActive)
                x-bind:class="{
                    'bg-gray-200 dark:bg-gray-600': ! ({{ $alpineActive }}),
                    'bg-white text-primary-600 font-medium': {{ $alpineActive }},
                }"
            @endif
            @class([
                'inline-flex items-center justify-center min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal',
                'bg-gray-200 dark:bg-gray-600' => (! $active) && (! $alpineActive),
                'bg-white text-primary-600 font-medium' => $active,
            ])
        >
            {{ $badge }}
        </span>
    @endif
</{{ $tag }}>
