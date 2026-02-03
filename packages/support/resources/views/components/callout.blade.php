@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\Components\CalloutComponent\IconComponent;

    use function Filament\Support\generate_icon_html;
    use function Filament\Support\is_slot_empty;
@endphp

@props([
    'color' => 'gray',
    'description' => null,
    'footer' => null,
    'heading' => null,
    'icon' => null,
    'iconColor' => null,
    'iconSize' => null,
])

@php
    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconColor ??= $color;

    $hasDescription = filled((string) $description);
    $hasHeading = filled($heading);
    $hasFooter = ! is_slot_empty($footer);
    $hasIcon = filled($icon);
@endphp

<div
    {{
        $attributes
            ->color(\Filament\Support\View\Components\CalloutComponent::class, $color)
            ->class(['fi-callout'])
    }}
>
    @if ($hasIcon)
        {{
            generate_icon_html(
                $icon,
                attributes: (new \Illuminate\View\ComponentAttributeBag)
                    ->color(IconComponent::class, $iconColor)
                    ->class(['fi-callout-icon']),
                size: $iconSize ?? IconSize::Large,
            )
        }}
    @endif

    @if ($hasHeading || $hasDescription || $hasFooter)
        <div class="fi-callout-main">
            @if ($hasHeading || $hasDescription)
                <div class="fi-callout-text">
                    @if ($hasHeading)
                        <h4 class="fi-callout-heading">
                            {{ $heading }}
                        </h4>
                    @endif

                    @if ($hasDescription)
                        <p class="fi-callout-description">
                            {{ $description }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($hasFooter)
                <div class="fi-callout-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    @endif
</div>
