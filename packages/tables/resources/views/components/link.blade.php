@props([
    'color' => 'primary',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'before',
    'tag' => 'a',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $linkClasses = [
        'inline-flex items-center justify-center hover:underline focus:outline-none focus:underline filament-tables-link',
        'opacity-70 cursor-not-allowed' => $disabled,
        'text-primary-600 hover:text-primary-500' => $color === 'primary',
        'text-danger-600 hover:text-danger-500' => $color === 'danger',
        'text-gray-600 hover:text-gray-500' => $color === 'secondary',
        'text-success-600 hover:text-success-500' => $color === 'success',
        'text-warning-600 hover:text-warning-500' => $color === 'warning',
        'dark:text-primary-500 dark:hover:text-primary-400' => $color === 'primary' && config('tables.dark_mode'),
        'dark:text-danger-500 dark:hover:text-danger-400' => $color === 'danger' && config('tables.dark_mode'),
        'dark:text-gray-500 dark:hover:text-gray-400' => $color === 'secondary' && config('tables.dark_mode'),
        'dark:text-success-500 dark:hover:text-success-400' => $color === 'success' && config('tables.dark_mode'),
        'dark:text-warning-500 dark:hover:text-warning-400' => $color === 'warning' && config('tables.dark_mode'),
    ];

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-icon w-4 h-4',
        'mr-1 -ml-2 rtl:ml-1 rtl:-mr-2' => $iconPosition === 'before',
        'ml-1 -mr-2 rtl:mr-1 rtl:-ml-2' => $iconPosition === 'after'
    ]);
@endphp

@if ($tag === 'a')
    <a
        @if ($tooltip)
            x-data="{}"
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{ $attributes->class($linkClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses"/>
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </a>
@elseif ($tag === 'button')
    <button
        @if ($tooltip)
            x-data="{}"
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        type="{{ $type }}"
        {!! $disabled ? 'disabled' : '' !!}
        {{ $attributes->class($linkClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses"/>
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </button>
@endif
