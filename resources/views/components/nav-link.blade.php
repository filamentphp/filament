@props([
    'activeRule',
    'icon',
    'label',
    'url',
])

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'py-2 px-2 flex items-center space-x-3 rounded transition-color duration-200 hover:text-white hover:bg-gray-900 ' . active($activeRule, 'text-white bg-gray-900', 'text-gray-400')]) }}>
    <x-dynamic-component :component="$icon" class="flex-shrink-0 w-5 h-5" />

    <span class="flex-grow leading-tight font-medium text-sm">{{ $label }}</span>
</a>
