@props([
    'activeRule',
    'icon',
    'label',
    'url',
])

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'py-2 px-3 flex items-center space-x-2 rounded transition-color duration-200 hover:text-white hover:bg-gray-800 ' . active($activeRule, 'text-white bg-gray-800', 'text-gray-400')]) }}>
    <x-dynamic-component :component="$icon" class="flex-shrink-0 w-4 h-4" />

    <span class="flex-grow text-sm leading-tight font-medium">{{ $label }}</span>
</a>
