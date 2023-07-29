@props([
    'size' => 'md',
    'src',
])

<div
    {{
        $attributes
            ->class([
                'fi-avatar bg-cover bg-center',
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
