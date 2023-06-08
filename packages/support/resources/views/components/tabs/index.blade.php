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
            ->class(['filament-tabs flex items-center space-x-1 overflow-x-auto rounded-lg bg-gray-600/5 p-1 text-sm text-gray-600 rtl:space-x-reverse dark:bg-gray-500/20'])
    }}
>
    {{ $slot }}
</nav>
