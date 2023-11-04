@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
    'iconSize' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'tag' => 'a',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = $iconPosition ? IconPosition::tryFrom($iconPosition) : null;
    }

    if (! $size instanceof ActionSize) {
        $size = ActionSize::tryFrom($size) ?? $size;
    }

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall, ActionSize::Small => IconSize::Small,
        default => IconSize::Medium,
    };

    if (! $iconSize instanceof IconSize) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $hasTooltip = filled($tooltip);

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    $loadingIndicatorTarget = $hasLoadingIndicator ? html_entity_decode($wireTarget ?: $form, ENT_QUOTES) : null;
@endphp

@capture($icons)
    @if ($icon)
        <x-filament::link.icon
            :alias="$iconAlias"
            :color="$color"
            :has-loading-indicator="$hasLoadingIndicator"
            :icon="$icon"
            :loading-indicator-target="$loadingIndicatorTarget"
            :size="$iconSize"
        />
    @endif

    @if ($hasLoadingIndicator)
        <x-filament::link.icon
            alias="link.loading-indicator"
            :color="$color"
            icon="heroicon-m-arrow-path"
            is-loading-indicator
            :loading-indicator-target="$loadingIndicatorTarget"
            :size="$iconSize"
        >
            {{-- <x-filament::loading-indicator /> --}}
        </x-filament::link.icon>
    @endif
@endcapture

<{{ $tag }}
    @if ($tag === 'a')
        {{ \Filament\Support\generate_href_html($href, $target === '_blank') }}
    @endif
    @if ($keyBindings || $hasTooltip)
        x-data="{}"
    @endif
    @if ($keyBindings)
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
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
                'disabled' => $tag === 'button' ? $disabled : null,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $hasLoadingIndicator,
                'wire:target' => $loadingIndicatorTarget,
            ], escape: false)
            ->class([
                'fi-link relative inline-flex items-center justify-center font-semibold outline-none transition duration-75 hover:underline focus-visible:underline',
                'pe-4' => $badge,
                'pointer-events-none opacity-70' => $disabled,
                "fi-size-{$size->value}" => $size instanceof ActionSize,
                // @deprecated `fi-link-size-*` has been replaced by `fi-size-*`.
                "fi-link-size-{$size->value}" => $size instanceof ActionSize,
                match ($size) {
                    ActionSize::ExtraSmall => 'gap-1 text-xs',
                    ActionSize::Small => 'gap-1 text-sm',
                    ActionSize::Medium => 'gap-1.5 text-sm',
                    ActionSize::Large => 'gap-1.5 text-sm',
                    ActionSize::ExtraLarge => 'gap-1.5 text-sm',
                    default => $size,
                },
                match ($color) {
                    'gray' => 'fi-color-gray text-gray-700 dark:text-gray-200',
                    default => 'fi-color-custom text-custom-600 dark:text-custom-400',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [300, 400, 500, 600],
                ) => $color !== 'gray',
            ])
    }}
>
    @if ($iconPosition === IconPosition::Before)
        {{ $icons() }}
    @endif

    {{ $slot }}

    @if ($iconPosition === IconPosition::After)
        {{ $icons() }}
    @endif

    @if (filled($badge))
        <div
            class="fi-link-badge-ctn absolute -top-1 start-full z-[1] -ms-1 w-max -translate-x-1/2 rounded-md bg-white rtl:translate-x-1/2 dark:bg-gray-900"
        >
            <x-filament::badge :color="$badgeColor" size="xs">
                {{ $badge }}
            </x-filament::badge>
        </div>
    @endif
</{{ $tag }}>
@trim
