@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
    use Filament\Support\View\Components\BadgeComponent;
    use Filament\Support\View\Components\DropdownComponent\ItemComponent;
    use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent;
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
    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconColor ??= $color;

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

{!! ($tag === 'form') ? ('<form ' . $attributes->only(['action', 'class', 'method', 'wire:submit'])->toHtml() . '>') : '' !!}

@if ($tag === 'form')
    @csrf
@endif

<{{ ($tag === 'form') ? 'button' : $tag }}
    @if (($tag === 'a') && (! ($disabled && $hasTooltip)))
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
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
            ->when(
                $tag === 'form',
                fn (ComponentAttributeBag $attributes) => $attributes->except(['action', 'class', 'method', 'wire:submit']),
            )
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'disabled' => $disabled && blank($tooltip),
                'type' => match ($tag) {
                    'button' => 'button',
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->when(
                $disabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-dropdown-list-item',
                'fi-disabled' => $disabled,
            ])
            ->color(ItemComponent::class, $color)
    }}
>
    @if ($icon)
        {{
            \Filament\Support\generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
            ]))->color(IconComponent::class, $iconColor), size: $iconSize)
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
            ]))->color(IconComponent::class, $iconColor), size: $iconSize)
        }}
    @endif

    <span class="fi-dropdown-list-item-label">
        {{ $slot }}
    </span>

    @if (filled($badge))
        @if ($badge instanceof \Illuminate\View\ComponentSlot)
            {{ $badge }}
        @else
            <span
                @if ($badgeTooltip)
                    x-tooltip="{
                        content: @js($badgeTooltip),
                        theme: $store.theme,
                    }"
                @endif
                {{ (new ComponentAttributeBag)->color(BadgeComponent::class, $badgeColor)->class(['fi-badge']) }}
            >
                {{ $badge }}
            </span>
        @endif
    @endif
</{{ ($tag === 'form') ? 'button' : $tag }}>

{!! ($tag === 'form') ? '</form>' : '' !!}
