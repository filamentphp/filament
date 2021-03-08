@props([
    'color' => 'white',
    'disabled' => false,
    'href' => null,
    'size' => 'base',
    'type' => 'button',
])

@php
    $colorClasses = [
        'danger' => 'border-transparent bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-200',
        'primary' => 'border-transparent bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-200',
        'white' => 'border-gray-300 bg-white text-gray-800 hover:bg-gray-100 focus:ring-primary-200',
    ][$color];

    $disabledClasses = $disabled ? 'opacity-25 cursor-not-allowed' : '';

    $sizeClasses = [
        'base' => 'text-sm py-2 px-4',
        'small' => 'text-xs py-1 px-3',
    ][$size];

    $classes = "cursor-pointer font-medium border rounded transition duration-200 shadow-sm focus:ring focus:ring-opacity-50 {$colorClasses} {$disabledClasses} {$sizeClasses}";
@endphp

@unless ($href)
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </a>
@endunless
