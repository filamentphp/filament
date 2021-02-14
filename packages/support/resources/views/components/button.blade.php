@props([
    'color' => 'white',
    'size' => 'base',
    'type' => 'button',
])

@php

$colorClasses = [
    'danger' => 'border-transparent from-red-700 to-red-800 text-white hover:to-red-700 focus:ring-red-200',
    'primary' => 'border-transparent from-orange-700 to-orange-800 text-white hover:to-orange-700 focus:ring-orange-200',
    'white' => 'border-gray-300 from-gray-100 to-gray-200 text-gray-800 hover:to-gray-100 focus:ring-blue-200',
][$color];

$sizeClasses = [
    'base' => 'text-sm py-2 px-4',
    'small' => 'text-xs py-1 px-3',
][$size];

@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "cursor-pointer font-medium border rounded transition duration-200 shadow-sm inline-block relative focus:ring focus:ring-opacity-50 bg-gradient-to-b {$colorClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</button>
