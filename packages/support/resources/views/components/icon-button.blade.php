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

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2',
        'pointer-events-none opacity-70' => $disabled,
        ...match ($size) {
            ActionSize::ExtraSmall => [
                match ($iconSize) {
                    IconSize::Small => '-m-1.5',
                    IconSize::Medium => '-m-1',
                    IconSize::Large => '-m-0.5',
                },
                'h-7 w-7',
            ],
            ActionSize::Small => [
                match ($iconSize) {
                    IconSize::Small => '-m-2',
                    IconSize::Medium => '-m-1.5',
                    IconSize::Large => '-m-1',
                },
                'h-8 w-8',
            ],
            ActionSize::Medium => [
                match ($iconSize) {
                    IconSize::Small => '-m-2.5',
                    IconSize::Medium => '-m-2',
                    IconSize::Large => '-m-1.5',
                },
                'h-9 w-9',
            ],
            ActionSize::Large => [
                match ($iconSize) {
                    IconSize::Small => '-m-3',
                    IconSize::Medium => '-m-2.5',
                    IconSize::Large => '-m-2',
                },
                'h-10 w-10',
            ],
            ActionSize::ExtraLarge => [
                match ($iconSize) {
                    IconSize::Small => '-m-3.5',
                    IconSize::Medium => '-m-3',
                    IconSize::Large => '-m-2.5',
                },
                'h-11 w-11',
            ],
        },
        match ($color) {
            'gray' => 'text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500',
            default => 'fi-color-custom text-custom-500 hover:text-custom-600 focus-visible:ring-custom-600 dark:text-custom-400 dark:hover:text-custom-300 dark:focus-visible:ring-custom-500',
        },
        is_string($color) ? "fi-color-{$color}" : null,
    ]);

    $buttonStyles = \Filament\Support\get_color_css_variables(
        $color,
        shades: [300, 400, 500, 600],
        alias: 'icon-button',
    );

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn-icon',
        match ($iconSize) {
            IconSize::Small => 'h-4 w-4',
            IconSize::Medium => 'h-5 w-5',
            IconSize::Large => 'h-6 w-6',
            default => $iconSize,
        },
    ]);

    $badgeContainerClasses = 'fi-icon-btn-badge-ctn absolute start-full top-1 z-[1] w-max -translate-x-1/2 -translate-y-1/2 rounded-md bg-white dark:bg-gray-900 rtl:translate-x-1/2';

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($tag === 'button')
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
                ], escape: false)
                ->merge([
                    'title' => $label,
                ], escape: true)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

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

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" :size="$badgeSize">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
@elseif ($tag === 'a')
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
        {{
            $attributes
                ->merge([
                    'title' => $label,
                ], escape: true)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :alias="$iconAlias"
            :icon="$icon"
            :class="$iconClasses"
        />

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@endif
