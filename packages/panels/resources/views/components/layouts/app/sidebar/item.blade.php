@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'hasGroupedBorder' => false,
    'last' => false,
    'first' => false,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url',
])

<li
    @class([
        'filament-sidebar-item',
        'filament-sidebar-item-active' => $active,
    ])
>
    <a
        href="{{ $url }}"
        @if ($shouldOpenUrlInNewTab) target="_blank" @endif
        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        @if (filament()->isSidebarCollapsibleOnDesktop())
            x-data="{ tooltip: {} }"
            x-init="
                Alpine.effect(() => {
                    if (Alpine.store('sidebar').isOpen) {
                        tooltip = false
                    } else {
                        tooltip = {
                            content: @js($slot->toHtml()),
                            theme: Alpine.store('theme') === 'light' ? 'dark' : 'light',
                            placement: document.dir === 'rtl' ? 'left' : 'right',
                        }
                    }
                })
            "
            x-tooltip.html="tooltip"
        @endif
        @class([
            'relative flex items-center justify-center gap-x-3 rounded-lg px-3 py-2 font-medium text-gray-700 outline-none transition hover:bg-gray-950/5 focus:bg-gray-950/5 dark:text-gray-300 dark:hover:bg-white/5 dark:focus:bg-white/5',
            'rounded-full bg-gray-950/5 text-primary-600 dark:bg-white/5 dark:text-primary-400' => $active,
        ])
    >
        @if ($icon)
            <x-filament::icon
                :name="($active && $activeIcon) ? $activeIcon : $icon"
                alias="panels::sidebar.item"
                color="text-custom-600 dark:text-custom-400"
                size="h-6 w-6"
                :style="\Filament\Support\get_color_css_variables(($active ? 'primary' : 'gray'), shades: [400, 600])"
            />
        @elseif ($hasGroupedBorder)
            <div
                class="filament-sidebar-item-grouped-border relative flex h-6 w-6 items-center justify-center"
            >
                @if (! $first)
                    <div
                        class="absolute -top-1/2 bottom-1/2 w-px bg-gray-300 dark:bg-gray-600"
                    ></div>
                @endif

                @if (! $last)
                    <div
                        class="absolute -bottom-1/2 top-1/2 w-px bg-gray-300 dark:bg-gray-600"
                    ></div>
                @endif

                <div
                    @class([
                        'relative h-1.5 w-1.5 rounded-full',
                        'bg-gray-400' => ! $active,
                        'bg-primary-600 dark:bg-primary-400' => $active,
                    ])
                ></div>
            </div>
        @endif

        <span
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="lg:transition delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            class="flex-1 text-sm"
        >
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::layouts.app.sidebar.badge
                :badge="$badge"
                :badge-color="$badgeColor"
                :active="$active"
            />
        @endif
    </a>
</li>
