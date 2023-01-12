@props([
    'label' => null,
])

<nav {{ $attributes
    ->merge([
        'aria-label' => $label,
        'role' => 'tablist',
    ])
    ->class(['filament-tabs flex overflow-x-auto items-center p-1 space-x-1 rtl:space-x-reverse text-sm text-gray-600 bg-gray-600/5 rounded-lg dark:bg-gray-500/20'])
}}>
    {{ $slot }}
</nav>
