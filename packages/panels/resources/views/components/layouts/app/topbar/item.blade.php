@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

@php
    $tag = $url ? 'a' : 'button';
@endphp

<li
    @class([
        'filament-topbar-item overflow-hidden',
        'filament-topbar-item-active' => $active,
    ])
>
    <{{ $tag }}
        @if ($url)
            href="{{ $url }}"
            @if ($shouldOpenUrlInNewTab) target="_blank" @endif
        @else
            type="button"
        @endif
        @class([
            'flex items-center justify-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 outline-none transition hover:bg-gray-950/5 focus:bg-gray-950/5 dark:text-gray-300 dark:hover:bg-white/5 dark:focus:bg-white/5',
            'bg-gray-950/5 text-primary-600 dark:bg-white/5 dark:text-primary-400' => $active,
        ])
    >
        @if ($icon || $activeIcon)
            <x-filament::icon
                :name="($active && $activeIcon) ? $activeIcon : $icon"
                alias="panels::topbar.item"
                color="text-custom-600 dark:text-custom-400"
                size="h-6 w-6"
                :style="\Filament\Support\get_color_css_variables(($active ? 'primary' : 'gray'), shades: [400, 600])"
            />
        @endif

        <div>
            {{ $slot }}
        </div>

        @if (filled($badge))
            <x-filament::layouts.app.topbar.badge
                :badge="$badge"
                :badge-color="$badgeColor"
                :active="$active"
            />
        @endif
    </{{ $tag }}>
</li>
