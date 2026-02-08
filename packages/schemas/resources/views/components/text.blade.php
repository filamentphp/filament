@php
    use Filament\Schemas\View\Components\TextComponent;
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\RawJs;

    $color = $getColor();
    $content = $getContent();
    $icon = $getIcon();
    $iconPosition = $getIconPosition();
    $iconSize = $getIconSize();
    $size = $getSize();
    $tooltip = $getTooltip();
    $weight = $getWeight();
    $fontFamily = $getFontFamily();

    $copyableState = $getCopyableState($content) ?? $content;
    $copyMessage = $getCopyMessage($copyableState);
    $copyMessageDuration = $getCopyMessageDuration($copyableState);
    $isCopyable = $isCopyable($copyableState);
@endphp

@if ($isBadge())
    <x-filament::badge
        :color="$color"
        :icon="$icon"
        :icon-position="$iconPosition"
        :icon-size="$iconSize"
        :size="$size instanceof \Filament\Support\Enums\TextSize ? $size->value : $size"
        :x-on:click="
            $isCopyable ? '
                window.navigator.clipboard.writeText(' . \Illuminate\Support\Js::from($copyableState) . ')
                $tooltip(' . \Illuminate\Support\Js::from($copyMessage) . ', {
                    theme: $store.theme,
                    timeout: ' . \Illuminate\Support\Js::from($copyMessageDuration) . ',
                })
            ' : null
        "
        :tag="$isCopyable ? 'button' : 'span'"
        :tooltip="$tooltip"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag()->class(['fi-sc-text']))"
    >
        {{ $content }}
    </x-filament::badge>
@else
    <span
        @if ($isCopyable)
            x-on:click="
                window.navigator.clipboard.writeText(@js($copyableState))
                $tooltip(@js($copyMessage), {
                    theme: $store.theme,
                    timeout: @js($copyMessageDuration),
                })
            "
        @endif
        @if (filled($tooltip))
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
                allowHTML: @js($tooltip instanceof \Illuminate\Contracts\Support\Htmlable),
            }"
        @endif
        {{
            (new \Illuminate\View\ComponentAttributeBag)
                ->color(TextComponent::class, $color)
                ->class([
                    'fi-sc-text',
                    'fi-copyable' => $isCopyable,
                    ($size instanceof \BackedEnum) ? "fi-size-{$size->value}" : $size,
                    ($weight instanceof FontWeight) ? "fi-font-{$weight->value}" : $weight,
                    ($fontFamily instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : $fontFamily,
                ])
                ->merge($getExtraAttributes(), escape: false)
        }}
    >
        {{ $content }}
    </span>
@endif
