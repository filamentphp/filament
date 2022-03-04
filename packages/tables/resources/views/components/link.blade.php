@props([
    'color' => 'primary',
    'tag' => 'a',
    'type' => 'button',
])

@php
    $linkClasses = [
        'hover:underline focus:outline-none focus:underline filament-tables-link',
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
@endphp

@if ($tag === 'a')
    <a {{ $attributes->class($linkClasses) }}>
        {{ $slot }}
    </a>
@elseif ($tag === 'button')
    <button
        type="{{ $type }}"
        {{ $attributes->class($linkClasses) }}
    >
        {{ $slot }}
    </button>
@endif
