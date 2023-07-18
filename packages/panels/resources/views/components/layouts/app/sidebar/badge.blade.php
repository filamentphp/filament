@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => 'primary',
])

<span
    @if (filament()->isSidebarCollapsibleOnDesktop())
        x-show="$store.sidebar.isOpen"
    @endif
    {{
        $attributes
            ->class([
                'inline-flex whitespace-normal rounded-full px-1.5 py-0.5 text-xs font-medium tracking-tighter bg-custom-500/20 text-custom-700 dark:text-custom-500',
            ])
            ->style([
                \Filament\Support\get_color_css_variables($badgeColor, shades: [500, 700]),
            ])
    }}
>
    {{ $badge }}
</span>
