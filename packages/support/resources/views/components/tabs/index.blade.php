@props([
    'contained' => false,
    'label' => null,
    'vertical' => false,
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
                'fi-vertical' => $vertical,
            ])
    }}
>
    {{ $slot }}
</nav>
