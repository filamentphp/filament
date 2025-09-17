@php
    use Filament\Support\Enums\IconPosition;
@endphp

@props([
    'active' => false,
    'alpineActive' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'badgeIcon' => null,
    'badgeIconPosition' => IconPosition::Before,
    'href' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconPosition' => IconPosition::Before,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    $hasAlpineActiveClasses = filled($alpineActive);
@endphp

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @elseif ($tag === 'a')
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($hasAlpineActiveClasses)
        x-bind:class="{
            'fi-active': {{ $alpineActive }},
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-selected' => $active,
                'role' => 'tab',
            ])
            ->class([
                'fi-tabs-item',
                'fi-active' => (! $hasAlpineActiveClasses) && $active,
            ])
    }}
>
    @if ($icon && $iconPosition === IconPosition::Before)
        {{ \Filament\Support\generate_icon_html($icon) }}
    @endif

    <span class="fi-tabs-item-label">
        {{ $slot }}
    </span>

    @if ($icon && $iconPosition === IconPosition::After)
        {{ \Filament\Support\generate_icon_html($icon) }}
    @endif

    @if (filled($badge))
        @if ($badge instanceof \Illuminate\View\ComponentSlot)
            {{ $badge }}
        @else
            <x-filament::badge
                :color="$badgeColor"
                :icon="$badgeIcon"
                :icon-position="$badgeIconPosition"
                size="sm"
                :tooltip="$badgeTooltip"
            >
                {{ $badge }}
            </x-filament::badge>
        @endif
    @endif
</{{ $tag }}>
