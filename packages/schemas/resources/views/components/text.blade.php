@php
    use Filament\Schemas\View\Components\TextComponent;
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\TextSize;
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

    if (!$iconSize && $size instanceof TextSize) {
        $iconSize = match($size) {
            TextSize::ExtraSmall => IconSize::ExtraSmall,
            TextSize::Small => IconSize::Small,
            TextSize::Medium => IconSize::Medium,
            TextSize::Large => IconSize::Large,
            default => IconSize::Small,
        };
    }

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
                    'inline-flex items-center gap-1.5' => filled($icon),
                    ($size instanceof \BackedEnum) ? "fi-size-{$size->value}" : $size,
                    ($weight instanceof FontWeight) ? "fi-font-{$weight->value}" : $weight,
                    ($fontFamily instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : $fontFamily,
                ])
                ->merge($getExtraAttributes(), escape: false)
        }}
    >
        @if ($icon && (!$iconPosition || $iconPosition === IconPosition::Before))
            {{ \Filament\Support\generate_icon_html($icon, attributes: new \Illuminate\View\ComponentAttributeBag, size: $iconSize ?? \Filament\Support\Enums\IconSize::Small) }}
        @endif

        {{ $content }}

        @if ($icon && $iconPosition === IconPosition::After)
            {{ \Filament\Support\generate_icon_html($icon, attributes: new \Illuminate\View\ComponentAttributeBag, size: $iconSize ?? \Filament\Support\Enums\IconSize::Small) }}
        @endif
    </span>
@endif
