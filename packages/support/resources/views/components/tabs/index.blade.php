@props([
    'contained' => false,
    'vertical' => false,
    'label' => null,
])

<nav
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class([
                'fi-tabs',
                'fi-contained' => $contained,
            ])
    }}
>
    {{ $slot }}
</nav>
