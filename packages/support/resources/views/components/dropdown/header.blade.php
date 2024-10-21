@php
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'color' => 'gray',
    'icon' => null,
    'iconSize' => IconSize::Medium,
    'tag' => 'div',
])

@php
    if (! ($iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }
@endphp

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header',
                match ($color) {
                    'gray' => '',
                    default => 'fi-color-custom',
                },
                is_string($color) ? "fi-color-{$color}" : null,
            ])
    }}
>
    {{
        \Filament\Support\generate_icon_html($icon, attributes: (new ComponentAttributeBag)
            ->class([
                ($iconSize instanceof IconSize) ? "fi-size-{$iconSize->value}" : (is_string($iconSize) ? $iconSize : null),
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [400, 500],
                    alias: 'dropdown.header.icon',
                ) => $color !== 'gray',
            ]))
    }}

    <span
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
