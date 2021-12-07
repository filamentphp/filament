@props([
    'color' => 'primary',
    'tag' => 'a',
    'type' => 'button',
])

@php
    $linkClasses = [
        'transition hover:underline focus:outline-none focus:underline',
        'text-primary-600 hover:text-primary-500' => $color === 'primary',
        'text-danger-600 hover:text-danger-500' => $color === 'danger',
        'text-gray-600 hover:text-gray-500' => $color === 'secondary',
        'text-success-600 hover:text-success-500' => $color === 'success',
        'text-warning-600 hover:text-warning-500' => $color === 'warning',
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
