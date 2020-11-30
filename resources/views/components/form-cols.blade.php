@props([
    'cols' => 2,
])

<div {{ $attributes->merge(['class' => 'grid grid-cols-1 gap-2 lg:gap-6 lg:grid-cols-'.$cols]) }}>
    {{ $slot }}
</div>