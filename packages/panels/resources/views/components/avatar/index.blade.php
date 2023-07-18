@props([
    'size' => null,
    'src',
])

<div
    {{
        $attributes
            ->class([
                'rounded-full bg-cover bg-center ring-1 ring-inset ring-gray-950/10 dark:ring-white/20',
                match ($size) {
                    null => 'h-9 w-9',
                    default => $size,
                },
            ])
            ->style([
                "background-image: url('{$src}')",
            ])
    }}
></div>
