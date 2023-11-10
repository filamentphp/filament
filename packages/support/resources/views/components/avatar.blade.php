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
                    'xs' => 'h-5 w-5',
                    'sm' => 'h-7 w-7',
                    'md' => 'h-9 w-9',
                    'lg' => 'h-10 w-10',
                    default => $size,
                },
            ])
    }}
/>
