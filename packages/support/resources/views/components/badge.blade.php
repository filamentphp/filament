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

    $iconClasses = ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : (is_string($iconSize) ? $iconSize : '');

    $isDeletable = count($deleteButton?->attributes->getAttributes() ?? []) > 0;

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
                'fi-badge',
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
            {{ \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
            ]))->class([$iconClasses])->style([$iconStyles])) }}
        @endif

        @if ($hasLoadingIndicator)
            {{ \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ]))->class([$iconClasses])->style([$iconStyles])) }}
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
            {{ $deleteButton->attributes
                ->except(['label'])
                ->class([
                    'fi-badge-delete-btn',
                ])
                ->style([
                    \Filament\Support\get_color_css_variables(
                        $color,
                        shades: [300, 700],
                        alias: 'badge.delete-button',
                    ) => $color !== 'gray',
                ]) }}
        >
            {{ \Filament\Support\generate_icon_html('heroicon-m-x-mark', alias: 'badge.delete-button') }}

            @if (filled($label = $deleteButton->attributes->get('label')))
                <span class="sr-only">
                    {{ $label }}
                </span>
            @endif
        </button>
    @elseif ($iconPosition === IconPosition::After)
        @if ($icon)
            {{ \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
            ]))->class([$iconClasses])->style([$iconStyles])) }}
        @endif

        @if ($hasLoadingIndicator)
            {{ \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ]))->class([$iconClasses])->style([$iconStyles])) }}
        @endif
    @endif
</{{ $tag }}>
