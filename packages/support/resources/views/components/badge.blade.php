@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'color' => 'primary',
    'deleteButton' => null,
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
    'iconSize' => IconSize::Small,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'spaMode' => null,
    'tag' => 'span',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $size instanceof ActionSize) {
        $size = filled($size) ? (ActionSize::tryFrom($size) ?? $size) : null;
    }

    if (! $iconSize instanceof IconSize) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }

    $isDeletable = count($deleteButton?->attributes->getAttributes() ?? []) > 0;

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-badge-icon h-4 w-4',
        match ($iconSize) {
            IconSize::Small => 'h-4 w-4',
            IconSize::Medium => 'h-5 w-5',
            IconSize::Large => 'h-6 w-6',
            default => $iconSize,
        },
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [500],
            alias: 'badge.icon',
        ) => $color !== 'gray',
    ]);

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
                'disabled' => $disabled,
                'form' => $tag === 'button' ? $formId : null,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->class([
                'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset',
                'pointer-events-none opacity-70' => $disabled,
                match ($size) {
                    ActionSize::ExtraSmall => 'px-0.5 min-w-[theme(spacing.4)] tracking-tighter',
                    ActionSize::Small => 'px-1.5 min-w-[theme(spacing.5)] py-0.5 tracking-tight',
                    ActionSize::Medium, ActionSize::Large, ActionSize::ExtraLarge => 'px-2 min-w-[theme(spacing.6)] py-1',
                    default => $size,
                },
                match ($color) {
                    'gray' => 'bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
                    default => 'fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30',
                },
                is_string($color) ? "fi-color-{$color}" : null,
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [
                        50,
                        400,
                        600,
                    ],
                    alias: 'badge',
                ) => $color !== 'gray',
            ])
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

    <span class="grid">
        <span class="truncate">
            {{ $slot }}
        </span>
    </span>

    @if ($isDeletable)
        <button
            type="button"
            {{
                $deleteButton
                    ->attributes
                    ->except(['label'])
                    ->class([
                        'fi-badge-delete-button -my-1 -me-2 -ms-1 flex items-center justify-center p-1 outline-none transition duration-75',
                        match ($color) {
                            'gray' => 'text-gray-700/50 hover:text-gray-700/75 focus-visible:text-gray-700/75 dark:text-gray-300/50 dark:hover:text-gray-300/75 dark:focus-visible:text-gray-300/75',
                            default => 'text-custom-700/50 hover:text-custom-700/75 focus-visible:text-custom-700/75 dark:text-custom-300/50 dark:hover:text-custom-300/75 dark:focus-visible:text-custom-300/75',
                        },
                    ])
                    ->style([
                        \Filament\Support\get_color_css_variables(
                            $color,
                            shades: [300, 700],
                            alias: 'badge.delete-button',
                        ) => $color !== 'gray',
                    ])
            }}
        >
            <x-filament::icon
                alias="badge.delete-button"
                icon="heroicon-m-x-mark"
                class="h-3.5 w-3.5"
            />

            @if (filled($label = $deleteButton->attributes->get('label')))
                <span class="sr-only">
                    {{ $label }}
                </span>
            @endif
        </button>
    @elseif ($iconPosition === IconPosition::After)
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
</{{ $tag }}>
