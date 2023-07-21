@props([
    'label' => null,
])

<nav
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class(['fi-tabs flex max-w-full gap-x-1 overflow-x-auto'])
    }}
>
    {{ $slot }}
</nav>
