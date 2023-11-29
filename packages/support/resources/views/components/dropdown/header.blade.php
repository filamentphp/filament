@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'color' => 'gray',
    'icon' => null,
    'iconSize' => IconSize::Medium,
    'tag' => 'div',
])

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header flex w-full gap-2 p-3 text-sm',
                match ($color) {
                    'gray' => 'fi-color-gray',
                    default => 'fi-color-custom',
                },
                // @deprecated `fi-dropdown-header-color-*` has been replaced by `fi-color-gray` and `fi-color-custom`.
                is_string($color) ? "fi-dropdown-header-color-{$color}" : null,
            ])
    }}
>
    @if (filled($icon))
        <x-filament::icon
            :icon="$icon"
            @class([
                'fi-dropdown-header-icon',
                match ($iconSize) {
                    IconSize::Small, 'sm' => 'h-4 w-4',
                    IconSize::Medium, 'md' => 'h-5 w-5',
                    IconSize::Large, 'lg' => 'h-6 w-6',
                    default => $iconSize,
                },
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-500 dark:text-custom-400',
                },
            ])
            @style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [400, 500],
                    alias: 'dropdown.header.icon',
                ) => $color !== 'gray',
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
        @style([
            \Filament\Support\get_color_css_variables(
                $color,
                shades: [400, 600],
                alias: 'dropdown.header.label',
            ) => $color !== 'gray',
        ])
    >
        {{ $slot }}
    </span>
</{{ $tag }}>
