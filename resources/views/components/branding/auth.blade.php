@props([
    'title',
])

<h2 {{ $attributes->merge(['class' => 'text-center text-2xl md:text-3xl leading-tight text-orange-700']) }}>{{ $title }}</h2>