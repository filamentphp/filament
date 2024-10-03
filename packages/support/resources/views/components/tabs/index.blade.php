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
                'fi-contained' => $contained && ! $vertical,
                'fi-contained border-r border-gray-200 px-3 py-2.5 dark:border-white/10' => $contained && $vertical,
                'flex-col' => $vertical,
            ])
    }}
>
    {{ $slot }}
</nav>
