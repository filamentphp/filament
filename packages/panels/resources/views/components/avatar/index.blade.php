@props([
    'src',
])

<div
    {{
        $attributes->class([
            'h-9 w-9 rounded-full bg-cover bg-center ring-1 ring-inset ring-gray-950/10 dark:ring-white/20',
        ])
    }}
    style="background-image: url('{{ $src }}')"
></div>
