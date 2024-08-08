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
            x-ignore
            {{--Set dimensions! Vertical sliders don't assume a default height, so a height needs to be set.--}}
            @class([
                'fi-slider-vh' => $orientation === \Filament\Forms\Enums\SliderOrientation::Vertical->value,
            ])>
        </div>
</x-dynamic-component>
