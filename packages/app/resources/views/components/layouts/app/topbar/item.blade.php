@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

<li @class(['filament-topbar-item overflow-hidden', 'filament-topbar-item-active' => $active])>
    <a
        @if ($url)
            href="{{ $url }}"
            @if ($shouldOpenUrlInNewTab) target="_blank" @endif
        @endif
        @class([
            'flex items-center justify-center gap-3 text-sm px-3 py-2 rounded-lg font-medium transition',
            'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700' => ! $active,
            'bg-primary-500 text-white' => $active,
        ])
    >
        @if ($icon || $activeIcon)
            <x-filament::icon
                :name="($active && $activeIcon) ? $activeIcon : $icon"
                alias="app::topbar.item"
                :color="(! $active) ? 'text-gray-600 dark:text-gray-500' : null"
                size="h-5 w-5"
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
    </a>
</li>
