@php
    use Illuminate\Support\Arr;
@endphp

@props([
    'state',
    'offColor' => 'gray',
    'offIcon' => null,
    'onColor' => 'primary',
    'onIcon' => null,
])

<button
    x-data="{ state: {{ $state }} }"
    x-bind:aria-checked="state?.toString()"
    x-on:click="state = ! state"
    x-bind:class="
        state ? @js(Arr::toCssClasses([
                    'fi-toggle-on',
                    match ($onColor) {
                        null, 'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($onColor) ? "fi-color-{$onColor}" : null,
                ])) : @js(Arr::toCssClasses([
                            'fi-toggle-off',
                            match ($offColor) {
                                null, 'gray' => null,
                                default => 'fi-color-custom bg-custom-600',
                            },
                            is_string($offColor) ? "fi-color-{$offColor}" : null,
                        ]))
    "
    x-bind:style="
        state ? @js(\Filament\Support\get_color_css_variables(
                    $onColor,
                    shades: [600],
                    alias: 'toggle.on',
                )) : @js(\Filament\Support\get_color_css_variables(
                            $offColor,
                            shades: [600],
                            alias: 'toggle.off',
                        ))
    "
    {{
        $attributes
            ->merge([
                'role' => 'switch',
                'type' => 'button',
            ], escape: false)
            ->class(['fi-toggle'])
    }}
>
    <div>
        <div aria-hidden="true">
            {{ \Filament\Support\generate_icon_html($offIcon) }}
        </div>

        <div aria-hidden="true">
            {{ \Filament\Support\generate_icon_html(
                $onIcon,
                attributes: (new \Illuminate\View\ComponentattributeBag())->merge(['x-cloak' => true], escape: false)), }}
        </div>
    </div>
</button>
