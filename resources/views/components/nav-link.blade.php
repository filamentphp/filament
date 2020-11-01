@props([
    'path',
    'active',
    'label',
    'heroicon'
])

<a href="#" {{ $attributes->merge(['class' => 'py-2 px-3 flex items-center space-x-3 rounded transition-color duration-200 hover:text-white hover:bg-gray-800'.active($active, ' text-white bg-gray-800', 'text-gray-400')]) }}>
    @if (!empty($heroicon))
        <x-dynamic-component :component="'heroicon-'.$heroicon" class="flex-shrink-0 w-4 h-4" />
    @endif
    <span class="flex-grow text-sm leading-tight font-semibold">{{ $label }}</span>
</a>