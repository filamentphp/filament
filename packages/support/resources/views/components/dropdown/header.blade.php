@props([
    'color' => 'gray',
    'icon' => null,
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
                \Filament\Support\get_color_css_variables($color, shades: [400, 600]) => $color !== 'gray',
            ])
    }}
>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            class="fi-dropdown-header-icon h-5 w-5"
        />
    @endif

    <span class="fi-dropdown-header-label">
        {{ $slot }}
    </span>
</{{ $tag }}>
