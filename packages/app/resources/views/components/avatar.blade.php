@props([
    'size' => 'md',
    'src',
])

<div
    {{ $attributes->class([
        'rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900',
        match ($size) {
            'xs' => 'w-6 h-6',
            'sm' => 'w-8 h-8',
            'md' => 'w-10 h-10',
            'lg' => 'w-12 h-12',
            default => $size,
        },
    ]) }}
    style="background-image: url('{{ $src }}')"
></div>
