@props([
    'activeRule',
    'icon',
    'label',
    'url',
])

<a
    href="{{ $url }}"
    {{ $attributes->merge(['class' => 'px-4 py-3 flex items-center space-x-3 rtl:space-x-reverse rounded transition-color duration-200 hover:text-white ' . (request()->is($activeRule) ? 'text-white bg-gray-900' : 'text-current')]) }}
>
    <x-dynamic-component :component="$icon" class="flex-shrink-0 w-5 h-5" />

    <span class="flex-grow text-sm font-medium leading-tight">{{ __($label) }}</span>
</a>
