@props([
    'title',
])

<h2 {{ $attributes->merge(['class' => 'text-center text-2xl md:text-3xl leading-tight text-primary-600']) }}>{{ $title }}</h2>
