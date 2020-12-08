@props([
    'path',
    'active',
    'label',
    'icon' => false,
])

<a href="{{ Route::has($path) ? route($path) : $path }}" {{ $attributes->merge(['class' => 'py-2 px-3 flex items-center space-x-2 rounded transition-color duration-200 hover:text-white hover:bg-gray-800 '.active($active, 'text-white bg-gray-800', 'text-gray-400')]) }}>
    @if ($icon)
        <x-dynamic-component :component="$icon" class="flex-shrink-0 w-4 h-4" />
    @endisset
    <span class="flex-grow text-sm leading-tight font-medium">{{ __($label) }}</span>
</a>