@php
    $fieldWrapperView = $getFieldWrapperView();
    $isVertical = $isVertical();
    $pipsMode = $getPipsMode();
    $livewireKey = $getLivewireKey();
    $isDisabled = $isDisabled();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('slider', 'filament/forms') }}"
        x-data="sliderFormComponent({
                    arePipsStepped: @js($arePipsStepped()),
                    behavior: @js($getBehaviorForJs()),
                    decimalPlaces: @js($getDecimalPlaces()),
                    fillTrack: @js($getFillTrack()),
                    isDisabled: @js($isDisabled),
                    isRtl: @js($isRtl()),
                    isVertical: @js($isVertical),
                    maxDifference: @js($getMaxDifference()),
                    minDifference: @js($getMinDifference()),
                    maxValue: @js($getMaxValue()),
                    minValue: @js($getMinValue()),
                    nonLinearPoints: @js($getNonLinearPoints()),
                    pipsDensity: @js($getPipsDensity()),
                    pipsFilter: @js($getPipsFilterForJs()),
                    pipsFormatter: @js($getPipsFormatterForJs()),
                    pipsMode: @js($pipsMode),
                    pipsValues: @js($getPipsValues()),
                    rangePadding: @js($getRangePadding()),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                    step: @js($getStep()),
                    tooltips: @js($getTooltipsForJs()),
                })"
        wire:ignore
        wire:key="{{ $livewireKey }}.{{
            substr(md5(serialize([
                $isDisabled,
            ])), 0, 64)
        }}"
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-slider',
                    'fi-fo-slider-has-pips' => $pipsMode,
                    'fi-fo-slider-has-tooltips' => $hasTooltips(),
                    'fi-fo-slider-vertical' => $isVertical,
                ])
        }}
    ></div>
</x-dynamic-component>
