@props([
    'color' => 'gray',
    'icon' => null,
    'iconSize' => 'md',
    'tag' => 'div',
])

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header flex w-full gap-2 p-3 text-sm',
                is_string($color) ? "fi-dropdown-header-color-{$color}" : null,
                match ($color) {
                    'gray' => 'text-gray-700 dark:text-gray-200',
                    default => 'text-custom-600 dark:text-custom-400',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]) => $color !== 'gray',
            ])
    }}
>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            @class([
                'fi-dropdown-header-icon',
                match ($iconSize) {
                    'sm' => 'h-4 w-4',
                    'md' => 'h-5 w-5',
                    'lg' => 'h-6 w-6',
                    default => $iconSize,
                },
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-500 dark:text-custom-400',
                },
            ])
        />
    @endif

    <span
        @class([
            'fi-dropdown-header-label flex-1 truncate text-start',
            match ($color) {
                'gray' => 'text-gray-700 dark:text-gray-200',
                default => 'text-custom-600 dark:text-custom-400',
            },
        ])
    >
        {{ $slot }}
    </span>
</{{ $tag }}>
