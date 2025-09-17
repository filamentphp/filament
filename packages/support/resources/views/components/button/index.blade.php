@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
    use Filament\Support\View\Components\BadgeComponent;
    use Filament\Support\View\Components\ButtonComponent;
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
    'labeledFrom' => null,
    'labelSrOnly' => false,
    'loadingIndicator' => true,
    'outlined' => false,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $size instanceof Size) {
        $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
    }

    if (! $badgeSize instanceof Size) {
        $badgeSize = filled($badgeSize) ? (Size::tryFrom($badgeSize) ?? $badgeSize) : null;
    }

    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconSize ??= match ($size) {
        Size::ExtraSmall, Size::Small => IconSize::Small,
        default => null,
    };

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasFormProcessingLoadingIndicator = $type === 'submit' && filled($form);
    $hasLoadingIndicator = filled($wireTarget) || $hasFormProcessingLoadingIndicator;

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($labeledFrom)
    <x-filament::icon-button
        :badge="$badge"
        :badge-color="$badgeColor"
        :badge-size="$badgeSize"
        :color="$color"
        :disabled="$disabled"
        :form="$form"
        :form-id="$formId"
        :href="$href"
        :icon="$icon"
        :icon-alias="$iconAlias"
        :icon-size="$iconSize"
        :key-bindings="$keyBindings"
        :label="$slot"
        :loading-indicator="$loadingIndicator"
        :size="$size"
        :spa-mode="$spaMode"
        :tag="$tag"
        :target="$target"
        :tooltip="$tooltip"
        :type="$type"
        :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    />
@endif

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
    @if ($hasFormProcessingLoadingIndicator)
        x-data="filamentFormButton"
        x-bind:class="{ 'fi-processing': isProcessing }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'aria-label' => $labelSrOnly ? trim(strip_tags($slot->toHtml())) : null,
                'disabled' => $disabled && blank($tooltip),
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                'x-bind:disabled' => $hasFormProcessingLoadingIndicator ? 'isProcessing' : null,
                'x-bind:aria-label' => ($labelSrOnly && $hasFormProcessingLoadingIndicator) ? ('isProcessing ? processingMessage : ' . \Illuminate\Support\Js::from(trim(strip_tags($slot->toHtml())))) : null,
            ], escape: false)
            ->when(
                $disabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-btn',
                'fi-disabled' => $disabled,
                'fi-outlined' => $outlined,
                ($size instanceof Size) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
                is_string($labeledFrom) ? "fi-labeled-from-{$labeledFrom}" : null,
            ])
            ->color(app(ButtonComponent::class, ['isOutlined' => $outlined]), $color)
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

        @if ($hasFormProcessingLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'x-cloak' => 'x-cloak',
                    'x-show' => 'isProcessing',
                ])), size: $iconSize)
            }}
        @endif
    @endif

    @if (! $labelSrOnly)
        @if ($hasFormProcessingLoadingIndicator)
            <span x-show="! isProcessing">
                {{ $slot }}
            </span>
        @else
            {{ $slot }}
        @endif
    @endif

    @if ($hasFormProcessingLoadingIndicator && (! $labelSrOnly))
        <span
            x-cloak
            x-show="isProcessing"
            x-text="processingMessage"
        ></span>
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

        @if ($hasFormProcessingLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'x-cloak' => 'x-cloak',
                    'x-show' => 'isProcessing',
                ])), size: $iconSize)
            }}
        @endif
    @endif

    @if (filled($badge))
        <div class="fi-btn-badge-ctn">
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
