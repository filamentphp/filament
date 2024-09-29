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

    $iconClasses = ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : (is_string($iconSize) ? $iconSize : '');

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
                'aria-label' => $labelSrOnly ? trim(strip_tags($slot->toHtml())) : null,
                'disabled' => $disabled,
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                'x-bind:disabled' => $hasFormProcessingLoadingIndicator ? 'isProcessing' : null,
                'x-bind:aria-label' => ($labelSrOnly && $hasFormProcessingLoadingIndicator) ? ('isProcessing ? processingMessage : ' . \Illuminate\Support\Js::from(trim(strip_tags($slot->toHtml())))) : null,
            ], escape: false)
            ->class([
                'fi-btn',
                'fi-disabled' => $disabled,
                'fi-outlined' => $outlined,
                match ($color) {
                    'gray' => '',
                    default => 'fi-color-custom',
                },
                is_string($color) ? "fi-color-{$color}" : null,
                ($size instanceof ActionSize) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
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
            {{
                \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                ]))->class([$iconClasses]))
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ]))->class([$iconClasses]))
            }}
        @endif

        @if ($hasFormProcessingLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'x-cloak' => 'x-cloak',
                    'x-show' => 'isProcessing',
                ]))->class([$iconClasses]))
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
                ]))->class([$iconClasses]))
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ]))->class([$iconClasses]))
            }}
        @endif

        @if ($hasFormProcessingLoadingIndicator)
            {{
                \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'x-cloak' => 'x-cloak',
                    'x-show' => 'isProcessing',
                ]))->class([$iconClasses]))
            }}
        @endif
    @endif

    @if (filled($badge))
        <div class="fi-btn-badge-ctn">
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
