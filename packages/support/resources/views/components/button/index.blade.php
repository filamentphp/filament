@php
    use Filament\Support\Enums\ActionSize;
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
    'grouped' => false,
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
    'size' => ActionSize::Medium,
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

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        ...[
            'fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2',
            'pointer-events-none opacity-70' => $disabled,
            'rounded-lg' => ! $grouped,
            'flex-1 [&:nth-child(1_of_.fi-btn)]:rounded-s-lg [&:nth-last-child(1_of_.fi-btn)]:rounded-e-lg [&:not(:nth-child(1_of_.fi-btn))]:shadow-[-1px_0_0_0_theme(colors.gray.200)] [&:not(:nth-last-child(1_of_.fi-btn))]:me-px dark:[&:not(:nth-child(1_of_.fi-btn))]:shadow-[-1px_0_0_0_theme(colors.white/20%)]' => $grouped,
            'cursor-pointer' => $tag === 'label',
            match ($color) {
                'gray' => null,
                default => 'fi-color-custom',
            },
            // @deprecated `fi-btn-color-*` has been replaced by `fi-color-*` and `fi-color-custom`.
            is_string($color) ? "fi-btn-color-{$color}" : null,
            is_string($color) ? "fi-color-{$color}" : null,
            ($size instanceof ActionSize) ? "fi-size-{$size->value}" : null,
            // @deprecated `fi-btn-size-*` has been replaced by `fi-size-*`.
            ($size instanceof ActionSize) ? "fi-btn-size-{$size->value}" : null,
            match ($size) {
                ActionSize::ExtraSmall => 'gap-1 px-2 py-1.5 text-xs',
                ActionSize::Small => 'gap-1 px-2.5 py-1.5 text-sm',
                ActionSize::Medium => 'gap-1.5 px-3 py-2 text-sm',
                ActionSize::Large => 'gap-1.5 px-3.5 py-2.5 text-sm',
                ActionSize::ExtraLarge => 'gap-1.5 px-4 py-3 text-sm',
                default => $size,
            },
            'hidden' => $labeledFrom,
            match ($labeledFrom) {
                'sm' => 'sm:inline-grid',
                'md' => 'md:inline-grid',
                'lg' => 'lg:inline-grid',
                'xl' => 'xl:inline-grid',
                '2xl' => '2xl:inline-grid',
                default => 'inline-grid',
            },
        ],
        ...(
            $outlined ?
                [
                    'fi-btn-outlined ring-1',
                    match ($color) {
                        'gray' => 'text-gray-950 ring-gray-300 hover:bg-gray-400/10 focus-visible:ring-gray-400/40 dark:text-white dark:ring-gray-700',
                        default => 'text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500',
                    },
                ] :
                [
                    'shadow-sm' => ! $grouped,
                    'bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10' => ($color === 'gray') || ($tag === 'label'),
                    'ring-1 ring-gray-950/10 dark:ring-white/20' => (($color === 'gray') || ($tag === 'label')) && (! $grouped),
                    'bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50' => ($color !== 'gray') && ($tag !== 'label'),
                    '[input:checked+&]:bg-custom-600 [input:checked+&]:text-white [input:checked+&]:ring-0 [input:checked+&]:hover:bg-custom-500 dark:[input:checked+&]:bg-custom-500 dark:[input:checked+&]:hover:bg-custom-400 [input:checked:focus-visible+&]:ring-custom-500/50 dark:[input:checked:focus-visible+&]:ring-custom-400/50 [input:focus-visible+&]:z-10 [input:focus-visible+&]:ring-2 [input:focus-visible+&]:ring-gray-950/10 dark:[input:focus-visible+&]:ring-white/20' => ($color !== 'gray') && ($tag === 'label'),
                    ]
        ),
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 500, 600],
            alias: 'button',
        ) => $color !== 'gray',
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-icon transition duration-75',
        match ($iconSize) {
            IconSize::Small => 'h-4 w-4',
            IconSize::Medium => 'h-5 w-5',
            IconSize::Large => 'h-6 w-6',
            default => $iconSize,
        },
        'text-gray-400 dark:text-gray-500' => ($color === 'gray') || ($tag === 'label'),
        'text-white' => ($color !== 'gray') && ($tag !== 'label') && (! $outlined),
        '[:checked+*>&]:text-white' => $tag === 'label',
    ]);

    $badgeContainerClasses = 'fi-btn-badge-ctn absolute start-full top-0 z-[1] w-max -translate-x-1/2 -translate-y-1/2 rounded-md bg-white dark:bg-gray-900 rtl:translate-x-1/2';

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-label',
        'sr-only' => $labelSrOnly,
    ]);

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
        :size="$size"
        :tag="$tag"
        :target="$target"
        :tooltip="$tooltip"
        :type="$type"
        :class="
            match ($labeledFrom) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
                default => 'hidden',
            }
        "
        :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    />
@endif

<{{ $tag }}
    @if ($tag === 'a')
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if (($keyBindings || $hasTooltip) && (! $hasFormProcessingLoadingIndicator))
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
    @if ($hasFormProcessingLoadingIndicator)
        x-data="{
            form: null,
            isProcessing: false,
            processingMessage: null,
        }"
        x-init="
            form = $el.closest('form')

            form?.addEventListener('form-processing-started', (event) => {
                isProcessing = true
                processingMessage = event.detail.message
            })

            form?.addEventListener('form-processing-finished', () => {
                isProcessing = false
            })
        "
        x-bind:class="{ 'enabled:opacity-70 enabled:cursor-wait': isProcessing }"
    @endif
    {{
        $attributes
            ->merge([
                'disabled' => $disabled,
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                'x-bind:disabled' => $hasFormProcessingLoadingIndicator ? 'isProcessing' : null,
            ], escape: false)
            ->class([$buttonClasses])
            ->style([$buttonStyles])
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

        @if ($hasFormProcessingLoadingIndicator)
            <x-filament::loading-indicator
                x-cloak="x-cloak"
                x-show="isProcessing"
                :class="$iconClasses"
            />
        @endif
    @endif

    <span
        @if ($hasFormProcessingLoadingIndicator)
            x-show="! isProcessing"
        @endif
        class="{{ $labelClasses }}"
    >
        {{ $slot }}
    </span>

    @if ($hasFormProcessingLoadingIndicator)
        <span
            x-cloak
            x-show="isProcessing"
            x-text="processingMessage"
            class="{{ $labelClasses }}"
        ></span>
    @endif

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

        @if ($hasFormProcessingLoadingIndicator)
            <x-filament::loading-indicator
                x-cloak="x-cloak"
                x-show="isProcessing"
                :class="$iconClasses"
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
</{{ $tag }}>
