@props([
    'size' => 'md',
    'src',
])

<div
    {{
        $attributes->class([
            'rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900',
            match ($size) {
                'xs' => 'h-6 w-6',
                'sm' => 'h-8 w-8',
                'md' => 'h-10 w-10',
                'lg' => 'h-12 w-12',
                default => $size,
            },
        ])
    }}
    style="background-image: url('{{ $src }}')"
></div>
