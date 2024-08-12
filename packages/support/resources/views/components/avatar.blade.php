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
                    'sm' => 'size-6',
                    'md' => 'size-8',
                    'lg' => 'size-10',
                    default => $size,
                },
            ])
    }}
/>
