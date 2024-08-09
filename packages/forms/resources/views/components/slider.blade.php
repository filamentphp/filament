@php
    use Filament\Support\Facades\FilamentView;

    $id = $getId();
    $statePath = $getStatePath();
    $range = $getRange();
    $step = $getStep();
    $start = $getStart();
    $margin = $getMargin();
    $limit = $getLimit();
    $connect = $getConnect();
    $direction = $getDirection();
    $orientation = $getOrientation();
    $behaviour = $getBehaviour();
    $tooltips = $getTooltips();
    $format = $getFormat();
    $pips = $getPips();
    $ariaFormat = $getAriaFormat();
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
        <div
            {{--Set dimensions! Vertical sliders don't assume a default height, so a height needs to be set.--}}
            {{--Set margin bottom when orientation is horizontal due to nouislider bug  --}}
            @class([
                'fi-slider-vh' => $orientation === \Filament\Forms\Enums\SliderOrientation::Vertical->value,
                'mb-8' => $orientation === \Filament\Forms\Enums\SliderOrientation::Horizontal->value,
            ])
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('slider', 'filament/forms') }}"
            x-data="sliderFormComponent({
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                statePath: @js($statePath),
                range: @js($range),
                step: @js($step),
                start: @js($start),
                margin: @js($margin),
                limit: @js($limit),
                connect: @js($connect),
                direction: @js($direction),
                orientation: @js($orientation),
                behaviour: @js($behaviour),
                tooltips: @js($tooltips),
                format: @js($format),
                pips: @js($pips),
                ariaFormat: @js($ariaFormat),
            })"
            {{
                $attributes
                    ->merge([
                        'disabled' => $isDisabled(),
                        'wire:target' => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
            }}
            wire:ignore
            x-ignore>
        </div>
    </x-dynamic-component>
