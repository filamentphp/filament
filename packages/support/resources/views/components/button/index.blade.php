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

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasFormProcessingLoadingIndicator = $type === 'submit' && filled($form);
    $hasLoadingIndicator = filled($wireTarget) || $hasFormProcessingLoadingIndicator;

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
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
        :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    />
@endif

<{{ $tag }}
    @if ($tag === 'a')
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings)
        x-bind:id="$id('key-bindings')"
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
    @endif
    @if (filled($tooltip))
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
            formElement = $el.closest('form')

            formElement?.addEventListener('form-processing-started', (event) => {
                isProcessing = true
                processingMessage = event.detail.message
            })

            formElement?.addEventListener('form-processing-finished', () => {
                isProcessing = false
            })
        "
        x-bind:class="{ 'fi-processing': isProcessing }"
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
            ->class([
                'fi-btn',
                'fi-disabled' => $disabled,
                'fi-outlined' => $outlined,
                match ($color) {
                    'gray' => null,
                    default => 'fi-color-custom',
                },
                is_string($color) ? "fi-color-{$color}" : null,
                ($size instanceof ActionSize) ? "fi-size-{$size->value}" : null,
                is_string($labeledFrom) ? "fi-labeled-from-{$labeledFrom}" : null,
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [400, 500, 600],
                    alias: 'button',
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
                    )->class([
                        ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                    ])
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
                    )->class([
                        'fi-icon',
                        ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                    ])
                "
            />
        @endif

        @if ($hasFormProcessingLoadingIndicator)
            <x-filament::loading-indicator
                x-cloak="x-cloak"
                x-show="isProcessing"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'fi-icon',
                    ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                ])"
            />
        @endif
    @endif

    <span
        @if ($hasFormProcessingLoadingIndicator)
            x-show="! isProcessing"
        @endif
        @class([
            'fi-btn-label',
            'sr-only' => $labelSrOnly,
        ])
    >
        {{ $slot }}
    </span>

    @if ($hasFormProcessingLoadingIndicator)
        <span
            x-cloak
            x-show="isProcessing"
            x-text="processingMessage"
            @class([
                'fi-btn-label',
                'sr-only' => $labelSrOnly,
            ])
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
                    )->class([
                        ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                    ])
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
                    )->class([
                        'fi-icon',
                        ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                    ])
                "
            />
        @endif

        @if ($hasFormProcessingLoadingIndicator)
            <x-filament::loading-indicator
                x-cloak="x-cloak"
                x-show="isProcessing"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'fi-icon',
                    ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : null,
                ])"
            />
        @endif
    @endif

    @if (filled($badge))
        <div class="fi-btn-badge-ctn">
            <x-filament::badge :color="$badgeColor" :size="$badgeSize">
                {{ $badge }}
            </x-filament::badge>
        </div>
    @endif
</{{ $tag }}>
