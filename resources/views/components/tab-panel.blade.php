@props([
    'id',
])

<div
    aria-labelledby="{{ $id }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ $id }}'"
    {{ $attributes->merge(['class' => 'p-4 md:p-6']) }}
>
    {{ $slot }}
</div>
