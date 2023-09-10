@props([
    'contained' => false,
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
                'fi-tabs flex max-w-full gap-x-1 overflow-x-auto',
                'fi-contained border-b border-gray-200 px-3 py-2.5 dark:border-white/10' => $contained,
                'mx-auto rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => ! $contained,
            ])
    }}
>
    {{ $slot }}
</nav>
