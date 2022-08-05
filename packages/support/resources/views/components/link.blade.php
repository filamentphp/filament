@props([
    'color' => 'primary',
    'darkMode' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'before',
    'keyBindings' => null,
    'size' => 'md',
    'tag' => 'a',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $linkClasses = [
        'inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline filament-link',
        'opacity-70 cursor-not-allowed pointer-events-none' => $disabled,
        'text-sm' => $size === 'sm',
        'text-lg' => $size === 'lg',
        'text-primary-600 hover:text-primary-500' => $color === 'primary',
        'text-danger-600 hover:text-danger-500' => $color === 'danger',
        'text-gray-600 hover:text-gray-500' => $color === 'secondary',
        'text-success-600 hover:text-success-500' => $color === 'success',
        'text-warning-600 hover:text-warning-500' => $color === 'warning',
        'dark:text-primary-500 dark:hover:text-primary-400' => $color === 'primary' && $darkMode,
        'dark:text-danger-500 dark:hover:text-danger-400' => $color === 'danger' && $darkMode,
        'dark:text-gray-300 dark:hover:text-gray-200' => $color === 'secondary' && $darkMode,
        'dark:text-success-500 dark:hover:text-success-400' => $color === 'success' && $darkMode,
        'dark:text-warning-500 dark:hover:text-warning-400' => $color === 'warning' && $darkMode,
    ];

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-icon',
        'w-3 h-3' => $size === 'sm',
        'w-4 h-4' => $size === 'md',
        'w-5 h-5' => $size === 'lg',
        'mr-1 rtl:ml-1' => $iconPosition === 'before',
        'ml-1 rtl:mr-1' => $iconPosition === 'after'
    ]);
@endphp

@if ($tag === 'a')
    <a
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
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
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        type="{{ $type }}"
        {!! $disabled ? 'disabled' : '' !!}
        @if ($keyBindings || $tooltip)
            x-data="{}"
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
    </button>
@endif
