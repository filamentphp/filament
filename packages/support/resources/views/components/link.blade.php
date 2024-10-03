@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
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
    'iconPosition' => IconPosition::Before,
    'iconSize' => null,
    'keyBindings' => null,
    'labelSrOnly' => false,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'spaMode' => null,
    'tag' => 'a',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
    'weight' => FontWeight::SemiBold,
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $size instanceof ActionSize) {
        $size = filled($size) ? (ActionSize::tryFrom($size) ?? $size) : null;
    }

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall, ActionSize::Small => IconSize::Small,
        default => IconSize::Medium,
    };

    if (! $iconSize instanceof IconSize) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }

    $linkClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-link group/link relative inline-flex items-center justify-center outline-none',
        'pointer-events-none opacity-70' => $disabled,
        ($size instanceof ActionSize) ? "fi-size-{$size->value}" : null,
        // @deprecated `fi-link-size-*` has been replaced by `fi-size-*`.
        ($size instanceof ActionSize) ? "fi-link-size-{$size->value}" : null,
        match ($size) {
            ActionSize::ExtraSmall => 'gap-1',
            ActionSize::Small => 'gap-1',
            ActionSize::Medium => 'gap-1.5',
            ActionSize::Large => 'gap-1.5',
            ActionSize::ExtraLarge => 'gap-1.5',
            default => $size,
        },
        match ($color) {
            'gray' => null,
            default => 'fi-color-custom',
        },
        is_string($color) ? "fi-color-{$color}" : null,
    ]);

    if (! $labelSrOnly) {
        $labelClasses = \Illuminate\Support\Arr::toCssClasses([
            match ($weight) {
                FontWeight::Thin, 'thin' => 'font-thin',
                FontWeight::ExtraLight, 'extralight' => 'font-extralight',
                FontWeight::Light, 'light' => 'font-light',
                FontWeight::Medium, 'medium' => 'font-medium',
                FontWeight::Normal, 'normal' => 'font-normal',
                FontWeight::SemiBold, 'semibold' => 'font-semibold',
                FontWeight::Bold, 'bold' => 'font-bold',
                FontWeight::ExtraBold, 'extrabold' => 'font-extrabold',
                FontWeight::Black, 'black' => 'font-black',
                default => $weight,
            },
            match ($size) {
                ActionSize::ExtraSmall => 'text-xs',
                ActionSize::Small => 'text-sm',
                ActionSize::Medium => 'text-sm',
                ActionSize::Large => 'text-sm',
                ActionSize::ExtraLarge => 'text-sm',
                default => null,
            },
            match ($color) {
                'gray' => 'text-gray-700 dark:text-gray-200',
                default => 'text-custom-600 dark:text-custom-400',
            },
            'group-hover/link:underline group-focus-visible/link:underline',
        ]);
    } else {
        $labelClasses = 'sr-only';
    }

    $labelStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 600],
            alias: 'link.label',
        ) => $color !== 'gray',
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-link-icon',
        match ($iconSize) {
            IconSize::Small => 'h-4 w-4',
            IconSize::Medium => 'h-5 w-5',
            IconSize::Large => 'h-6 w-6',
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
            shades: [400, 600],
            alias: 'link.icon',
        ) => $color !== 'gray',
    ]);

    $badgeContainerClasses = 'fi-link-badge-ctn absolute start-full top-0 z-[1] w-max -translate-x-1/4 -translate-y-3/4 rounded-md bg-white dark:bg-gray-900 rtl:translate-x-1/4';

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($tag === 'a')
    <a
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
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
        {{ $attributes->class([$linkClasses]) }}
    >
        @if ($icon && $iconPosition === IconPosition::Before)
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
                :style="$iconStyles"
            />
        @endif

        <span class="{{ $labelClasses }}" style="{{ $labelStyles }}">
            {{ $slot }}
        </span>

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
                <x-filament::badge :color="$badgeColor" :size="$badgeSize">
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
                    'disabled' => $disabled,
                    'form' => $formId,
                    'type' => $type,
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([$linkClasses])
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
                        )
                            ->class([$iconClasses])
                            ->style([$iconStyles])
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
                        )
                            ->class([$iconClasses])
                            ->style([$iconStyles])
                    "
                />
            @endif
        @endif

        <span class="{{ $labelClasses }}" style="{{ $labelStyles }}">
            {{ $slot }}
        </span>

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
                        )
                            ->class([$iconClasses])
                            ->style([$iconStyles])
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
                        )
                            ->class([$iconClasses])
                            ->style([$iconStyles])
                    "
                />
            @endif
        @endif

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" :size="$badgeSize">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
    @trim
@endif
