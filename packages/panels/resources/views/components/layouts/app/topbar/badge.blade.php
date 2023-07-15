@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => 'primary',
])

<span
    {{
        $attributes
            ->class([
                'min-h-4 ms-auto inline-flex items-center justify-center whitespace-normal rounded-xl px-2 py-0.5 text-xs font-medium tracking-tight',
                match ($active) {
                    true => 'bg-gray-900/10 text-white',
                    false => 'bg-custom-500/10 text-custom-700 dark:text-custom-500',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($badgeColor, shades: [500, 700]) => ! $active,
            ])
    }}
>
    {{ $badge }}
</span>
