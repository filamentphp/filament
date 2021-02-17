@props([
    'color' => 'white',
    'href' => null,
    'size' => 'base',
    'type' => 'button',
])

@php

$colorClasses = [
    'danger' => 'border-transparent bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-200',
    'primary' => 'border-transparent bg-primary text-white hover:bg-primary-600 focus:ring-primary-200',
    'secondary' => 'border-transparent bg-secondary-600 text-white hover:bg-secondary focus:ring-secondary-200',
    'white' => 'border-gray-300 bg-gray-50 text-gray-800 hover:bg-gray-100 focus:ring-primary-200',
][$color];

$sizeClasses = [
    'base' => 'text-sm py-2 px-4',
    'small' => 'text-xs py-1 px-3',
][$size];

$classes = "cursor-pointer font-medium border rounded transition duration-200 shadow-sm focus:ring focus:ring-opacity-50 {$colorClasses} {$sizeClasses}"

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
