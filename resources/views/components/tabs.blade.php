@props([
    'label',
    'tab',
])

<div 
    x-data="{ tab: '{{ $tab }}' }" 
    x-cloak
    {{ $attributes->merge(['class' => 'bg-white shadow rounded overflow-hidden']) }}
>
    <div class="bg-gray-100 border-b border-gray-200 flex" class="tablist" role="tablist" aria-label="{{ $label }}">
        {{ $tablist }}
    </div>
    {{ $slot }}
</div>