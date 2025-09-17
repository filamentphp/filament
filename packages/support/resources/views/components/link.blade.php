@php
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
    use Filament\Support\View\Components\BadgeComponent;
    use Filament\Support\View\Components\LinkComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeSize' => Size::ExtraSmall,
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
    'iconSize' => null,
    'keyBindings' => null,
    'labelSrOnly' => false,
    'loadingIndicator' => true,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'a',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
    'weight' => null,
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $badgeSize instanceof Size) {
        $badgeSize = filled($badgeSize) ? (Size::tryFrom($badgeSize) ?? $badgeSize) : null;
    }

    if (! $size instanceof Size) {
        $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
    }

    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconSize ??= match ($size) {
        Size::ExtraSmall, Size::Small => IconSize::Small,
        default => null,
    };

    if (! $weight instanceof FontWeight) {
        $weight = filled($weight) ? (FontWeight::tryFrom($weight) ?? $weight) : null;
    }

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

<{{ $tag }}
    @if (($tag === 'a') && (! ($disabled && $hasTooltip)))
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings)
        x-bind:id="$id('key-bindings')"
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
    @endif
    @if ($hasTooltip)
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'disabled' => $disabled && blank($tooltip),
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->when(
                $disabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-link',
                'fi-disabled' => $disabled,
                ($size instanceof Size) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
                ($weight instanceof FontWeight) ? "fi-font-{$weight->value}" : (is_string($weight) ? $weight : ''),
            ])
            ->color(LinkComponent::class, $color)
    }}
>
    @if ($iconPosition === IconPosition::Before)
        @if ($icon)
            {{
                \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                ])), size: $iconSize)
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ])), size: $iconSize)
            }}
        @endif
    @endif

    @if (! $labelSrOnly)
        {{ $slot }}
    @endif

    @if ($iconPosition === IconPosition::After)
        @if ($icon)
            {{
                \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                ])), size: $iconSize)
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ])), size: $iconSize)
            }}
        @endif
    @endif

    @if (filled($badge))
        <div class="fi-link-badge-ctn">
            @if ($badge instanceof \Illuminate\View\ComponentSlot)
                {{ $badge }}
            @else
                <span
                    {{
                        (new ComponentAttributeBag)->color(BadgeComponent::class, $badgeColor)->class([
                            'fi-badge',
                            ($badgeSize instanceof Size) ? "fi-size-{$badgeSize->value}" : (is_string($badgeSize) ? $badgeSize : ''),
                        ])
                    }}
                >
                    {{ $badge }}
                </span>
            @endif
        </div>
    @endif
</{{ $tag }}>
@trim
