@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeSize' => 'xs',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => null,
    'keyBindings' => null,
    'label' => null,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $size instanceof ActionSize) {
        $size = filled($size) ? (ActionSize::tryFrom($size) ?? $size) : null;
    }

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall => IconSize::Small,
        ActionSize::Small, ActionSize::Medium => IconSize::Medium,
        ActionSize::Large, ActionSize::ExtraLarge => IconSize::Large,
        default => IconSize::Medium,
    };

    if (! $iconSize instanceof IconSize) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }

    $iconClasses = ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : (is_string($iconSize) ? $iconSize : '');

    if (! $badgeSize instanceof ActionSize) {
        $badgeSize = filled($badgeSize) ? (ActionSize::tryFrom($badgeSize) ?? $badgeSize) : null;
    }

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

<{{ $tag }}
    @if ($tag === 'a')
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings || $hasTooltip)
        x-data="{}"
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
                'aria-label' => $label,
                'disabled' => $disabled,
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
            ], escape: false)
            ->merge([
                'title' => $label,
            ], escape: true)
            ->class([
                'fi-icon-btn',
                'fi-disabled' => $disabled,
                match ($color) {
                    'gray' => '',
                    default => 'fi-color-custom',
                },
                is_string($color) ? "fi-color-{$color}" : null,
                ($size instanceof ActionSize) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [300, 400, 500, 600],
                    alias: 'icon-button',
                ),
            ])
    }}
>
    {{
        \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ]))->class([$iconClasses]))
    }}

    @if ($hasLoadingIndicator)
        {{
            \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ]))->class([$iconClasses]))
        }}
    @endif

    @if (filled($badge))
        <div class="fi-icon-btn-badge-ctn">
            <span
                @class([
                    'fi-badge',
                    match ($badgeColor) {
                        'gray' => '',
                        default => 'fi-color-custom',
                    },
                    is_string($badgeColor) ? "fi-color-{$badgeColor}" : null,
                    ($badgeSize instanceof ActionSize) ? "fi-size-{$badgeSize->value}" : (is_string($badgeSize) ? $badgeSize : ''),
                ])
                @style([
                    \Filament\Support\get_color_css_variables(
                        $badgeColor,
                        shades: [
                            50,
                            400,
                            600,
                        ],
                        alias: 'badge',
                    ) => $badgeColor !== 'gray',
                ])
            >
                {{ $badge }}
            </span>
        </div>
    @endif
</{{ $tag }}>
