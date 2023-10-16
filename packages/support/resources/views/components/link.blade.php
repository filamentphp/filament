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

    $linkClasses = \Illuminate\Support\Arr::toCssClasses([
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
    ]);

    $linkStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [300, 400, 500, 600],
        ) => $color !== 'gray',
    ]);

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall, ActionSize::Small, 'xs', 'sm' => IconSize::Small,
        default => IconSize::Medium,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-link-icon',
        match ($iconSize) {
            IconSize::Small, 'sm' => 'h-4 w-4',
            IconSize::Medium, 'md' => 'h-5 w-5',
            IconSize::Large, 'lg' => 'h-6 w-6',
            default => $iconSize,
        },
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-600 dark:text-custom-400',
        },
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [500],
        ) => $color !== 'gray',
    ]);

    $badgeContainerClasses = 'fi-link-badge-ctn absolute -top-1 start-full z-[1] -ms-1 w-max -translate-x-1/2 rounded-md bg-white rtl:translate-x-1/2 dark:bg-gray-900';

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($tag === 'a')
    <a
        {{ \Filament\Support\generate_href_html($href, $target === '_blank') }}
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
                ->class([$linkClasses])
                ->style([$linkStyles])
        }}
    >
        @if ($icon && $iconPosition === IconPosition::Before)
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
                :style="$iconStyles"
            />
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === IconPosition::After)
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
                :style="$iconStyles"
            />
        @endif

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
    @trim
@elseif ($tag === 'button')
    <button
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
                    'disabled' => $disabled,
                    'type' => $type,
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([$linkClasses])
                ->style([$linkStyles])
        }}
    >
        @if ($iconPosition === IconPosition::Before)
            @if ($icon)
                <x-filament::icon
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'alias' => $iconAlias,
                                'icon' => $icon,
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : null,
                            ])
                        )->class([$iconClasses])
                    "
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => $loadingIndicatorTarget,
                            ])
                        )->class([$iconClasses])
                    "
                />
            @endif
        @endif

        {{ $slot }}

        @if ($iconPosition === IconPosition::After)
            @if ($icon)
                <x-filament::icon
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'alias' => $iconAlias,
                                'icon' => $icon,
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : null,
                            ])
                        )->class([$iconClasses])
                    "
                    :style="$iconStyles"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => $loadingIndicatorTarget,
                            ])
                        )->class([$iconClasses])
                    "
                    :style="$iconStyles"
                />
            @endif
        @endif

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
    @trim
@endif
