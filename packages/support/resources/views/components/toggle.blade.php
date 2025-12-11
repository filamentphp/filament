@php
    use Filament\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;
@endphp

@props([
    'state',
    'offColor' => 'gray',
    'offIcon' => null,
    'onColor' => 'primary',
    'onIcon' => null,
])

@php
    $onColorClasses = [];
    $onColorStyles = [];

    $offColorClasses = [];
    $offColorStyles = [];

    if (is_array($onColor)) {
        $onColorClasses[] = 'fi-color';
        $onColorStyles = \Filament\Support\get_component_custom_styles(ToggleComponent::class, $onColor);
    } else {
        $onColorClasses = \Filament\Support\get_component_color_classes(ToggleComponent::class, $onColor);
    }

    if (is_array($offColor)) {
        $offColorClasses[] = 'fi-color';
        $offColorStyles = \Filament\Support\get_component_custom_styles(ToggleComponent::class, $offColor);
    } else {
        $offColorClasses = \Filament\Support\get_component_color_classes(ToggleComponent::class, $offColor);
    }
@endphp

<button
    x-data="{ state: {{ $state }} }"
    x-bind:aria-checked="state?.toString()"
    x-on:click="state = ! state"
    x-bind:class="state ? @js(Arr::toCssClasses(['fi-toggle-on', ...$onColorClasses])) : @js(Arr::toCssClasses(['fi-toggle-off', ...$offColorClasses]))"
    x-bind:style="state ? @js(Arr::toCssStyles($onColorStyles)) : @js(Arr::toCssStyles($offColorStyles))"
    @if ($state)
        x-cloak
    @endif
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
            {{ \Filament\Support\generate_icon_html($offIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
        </div>

        <div aria-hidden="true">
            {{ \Filament\Support\generate_icon_html($onIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
        </div>
    </div>
</button>

@if ($state)
    <div
        x-cloak="inline-flex"
        wire:ignore
        @class([
            'fi-toggle fi-toggle-on fi-hidden',
            ...$onColorClasses,
        ])
        @style($onColorStyles)
    >
        <div>
            <div aria-hidden="true"></div>

            <div aria-hidden="true">
                {{ \Filament\Support\generate_icon_html($onIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
            </div>
        </div>
    </div>
@endif
