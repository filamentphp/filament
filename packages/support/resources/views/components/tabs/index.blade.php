@props([
    'contained' => false,
    'label' => null,
    'vertical' => false,
])

<nav
    {{
        $attributes
            ->merge([
                'aria-label' => $label ?? __('filament::components/tabs.label'),
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
