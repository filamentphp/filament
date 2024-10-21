@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeTooltip' => null,
    'color' => 'gray',
    'disabled' => false,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => null,
    'iconSize' => null,
    'image' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
])

@php
    if (! $iconSize instanceof IconSize) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }

    $iconColor ??= $color;

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-dropdown-list-item-icon',
        ($iconSize instanceof IconSize) ? ('fi-size-' . $iconSize->value) : (is_string($iconSize) ? $iconSize : ''),
        match ($iconColor) {
            'gray' => '',
            default => 'fi-color-custom',
        },
        is_string($iconColor) ? "fi-color-{$iconColor}" : null,
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $iconColor,
            shades: [400, 500],
            alias: 'dropdown.list.item.icon',
        ) => $iconColor !== 'gray',
    ]);

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }
@endphp

{!! ($tag === 'form') ? ('<form ' . $attributes->only(['action', 'class', 'method', 'wire:submit'])->toHtml() . '>') : '' !!}

@if ($tag === 'form')
    @csrf
@endif

<{{ ($tag === 'form') ? 'button' : $tag }}
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
    {{
        $attributes
            ->when(
                $tag === 'form',
                fn (ComponentAttributeBag $attributes) => $attributes->except(['action', 'class', 'method', 'wire:submit']),
            )
            ->merge([
                'disabled' => $disabled,
                'type' => match ($tag) {
                    'button' => 'button',
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->class([
                'fi-dropdown-list-item',
                'fi-disabled' => $disabled,
                match ($color) {
                    'gray' => '',
                    default => 'fi-color-custom',
                },
                is_string($color) ? "fi-color-{$color}" : null,
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [50, 400],
                    alias: 'dropdown.list.item',
                ) => $color !== 'gray',
            ])
    }}
>
    @if ($icon)
        {{
            \Filament\Support\generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
            ]))->class([$iconClasses])->style([$iconStyles]))
        }}
    @endif

    @if ($image)
        <div
            class="fi-dropdown-list-item-image"
            style="background-image: url('{{ $image }}')"
            @if ($hasLoadingIndicator)
                wire:loading.remove.delay.{{ config('filament.livewire_loading_delay', 'default') }}
                wire:target="{{ $loadingIndicatorTarget }}"
            @endif
        ></div>
    @endif

    @if ($hasLoadingIndicator)
        {{
            \Filament\Support\generate_loading_indicator_html((new ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ]))->class([$iconClasses])->style([$iconStyles]))
        }}
    @endif

    <span
        class="fi-dropdown-list-item-label"
        @style([
            \Filament\Support\get_color_css_variables(
                $color,
                shades: [400, 600],
                alias: 'dropdown.list.item.label',
            ) => $color !== 'gray',
        ])
    >
        {{ $slot }}
    </span>

    @if (filled($badge))
        <span
            @if ($badgeTooltip)
                x-tooltip="{
                    content: @js($badgeTooltip),
                    theme: $store.theme,
                }"
            @endif
            @class([
                'fi-badge',
                match ($badgeColor) {
                    'gray' => '',
                    default => 'fi-color-custom',
                },
                is_string($badgeColor) ? "fi-color-{$badgeColor}" : null,
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
    @endif
</{{ ($tag === 'form') ? 'button' : $tag }}>

{!! ($tag === 'form') ? '</form>' : '' !!}
