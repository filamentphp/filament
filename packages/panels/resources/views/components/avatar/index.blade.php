@props([
    'size' => 'md',
    'src',
])

<div
    {{
        $attributes
            ->class([
                'fi-avatar bg-cover bg-center ring-1 ring-inset ring-gray-950/10 dark:ring-white/20',
                match ($size) {
                    'md' => 'h-9 w-9',
                    'lg' => 'h-10 w-10',
                    default => $size,
                },
            ])
            ->style([
                "background-image: url('{$src}')",
            ])
    }}
></div>
