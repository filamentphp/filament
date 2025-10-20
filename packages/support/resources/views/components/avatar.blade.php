@props([
    'circular' => true,
    'size' => 'md',
])

<img
    {{
        $attributes
            ->class([
                'fi-avatar',
                'fi-circular' => $circular,
                match ($size) {
                    'sm', 'md', 'lg' => "fi-size-{$size}",
                    default => $size,
                },
            ])
    }}
/>
