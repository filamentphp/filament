@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

@php
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag;
    use Filament\View\PanelsIconAlias;

    $tag = $url ? 'a' : 'button';
@endphp

<li
    {{
        $attributes->class([
            'fi-topbar-item',
            'fi-active' => $active,
        ])
    }}
>
    <{{ $tag }}
        @if ($url)
            {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
            @if ($active)
                aria-current="page"
            @endif
        @else
            type="button"
        @endif
        class="fi-topbar-item-btn"
    >
        @if ($icon || $activeIcon)
            {{ \Filament\Support\generate_icon_html(($active && $activeIcon) ? $activeIcon : $icon, attributes: (new ComponentAttributeBag)->class(['fi-topbar-item-icon'])) }}
        @endif

        <span class="fi-topbar-item-label">
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::badge
                :color="$badgeColor"
                size="sm"
                :tooltip="$badgeTooltip"
            >
                {{ $badge }}
            </x-filament::badge>
        @endif

        @if (! $url)
            {{ \Filament\Support\generate_icon_html(Heroicon::ChevronDown, alias: PanelsIconAlias::TOPBAR_GROUP_TOGGLE_BUTTON, attributes: (new ComponentAttributeBag)->class(['fi-topbar-group-toggle-icon'])) }}
        @endif
    </{{ $tag }}>
</li>
