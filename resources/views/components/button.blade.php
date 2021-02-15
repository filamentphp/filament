@props([
    'color' => 'white',
    'href' => null,
    'size' => 'base',
    'type' => 'button',
])

@php

$colorClasses = [
    'danger' => 'border-transparent from-danger-700 to-danger-800 text-white hover:to-danger-700 focus:ring-danger-200',
    'primary' => 'border-transparent from-primary-700 to-primary-800 text-white hover:to-primary-700 focus:ring-primary-200',
    'secondary' => 'border-transparent from-secondary-700 to-secondary-800 text-white hover:to-secondary-700 focus:ring-secondary-200',
    'white' => 'border-gray-300 from-gray-100 to-gray-200 text-gray-800 hover:to-gray-100 focus:ring-primary-200',
][$color];

$sizeClasses = [
    'base' => 'text-sm py-2 px-4',
    'small' => 'text-xs py-1 px-3',
][$size];

$classes = "cursor-pointer font-medium border rounded transition duration-200 shadow-sm inline-block relative focus:ring focus:ring-opacity-50 bg-gradient-to-b {$colorClasses} {$sizeClasses}"

@endphp

@unless ($href)
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endunless
