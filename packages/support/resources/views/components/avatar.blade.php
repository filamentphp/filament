@props([
    'circular' => true,
    'size' => 'md',
])

<img
    {{
        $attributes
            ->class([
                'fi-avatar object-cover object-center',
                'rounded-md' => ! $circular,
                'fi-circular rounded-full' => $circular,
                match ($size) {
                    'md' => 'h-9 w-9',
                    'lg' => 'h-10 w-10',
                    default => $size,
                },
            ])
    }}
/>
