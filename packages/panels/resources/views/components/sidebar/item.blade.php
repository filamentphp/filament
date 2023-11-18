@props([
    'active' => false,
    'activeIcon' => null,
    'activeChildItems' => false,
    'badge' => null,
    'badgeColor' => null,
    'childItems' => [],
    'grouped' => false,
    'subGrouped' => false,
    'last' => false,
    'first' => false,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url',
])

<li
    {{
        $attributes->class([
            'fi-sidebar-item',
            // @deprecated `fi-sidebar-item-active` has been replaced by `fi-active`.
            'fi-active fi-sidebar-item-active' => $active,
            'flex flex-col gap-y-1' => $active || $activeChildItems,
        ])
    }}
>
    <a
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        @if (filament()->isSidebarCollapsibleOnDesktop())
            x-data="{ tooltip: false }"
            x-effect="
                tooltip = $store.sidebar.isOpen
                    ? false
                    : {
                          content: @js($slot->toHtml()),
                          placement: document.dir === 'rtl' ? 'left' : 'right',
                          theme: $store.theme,
                      }
            "
            x-tooltip.html="tooltip"
        @endif
        @class([
            'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 text-sm outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5',
            'bg-gray-100 dark:bg-white/5' => $active,
        ])
    >
        @if (filled($icon) && ((! $subGrouped) || filament()->isSidebarCollapsibleOnDesktop()))
            <x-filament::icon
                :x-show="($subGrouped && filament()->isSidebarCollapsibleOnDesktop()) ? '! $store.sidebar.isOpen' : false"
                :icon="($active && $activeIcon) ? $activeIcon : $icon"
                @class([
                    'fi-sidebar-item-icon h-6 w-6',
                    'text-gray-400 dark:text-gray-500' => ! $active,
                    'text-primary-600 dark:text-primary-400' => $active,
                ])
            />
        @endif

        @if ((blank($icon) && $grouped) || $subGrouped)
            <div
                @if (filled($icon) && $subGrouped && filament()->isSidebarCollapsibleOnDesktop())
                    x-show="$store.sidebar.isOpen"
                @endif
                class="fi-sidebar-item-grouped-border relative flex h-6 w-6 items-center justify-center"
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
                        'bg-gray-400 dark:bg-gray-500' => ! $active,
                        'bg-primary-600 dark:bg-primary-400' => $active,
                    ])
                ></div>
            </div>
        @endif

        <span
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="lg:transition lg:delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            @class([
                'fi-sidebar-item-label flex-1 truncate',
                'text-gray-700 dark:text-gray-200' => ! $active,
                'text-primary-600 dark:text-primary-400' => $active,
                'font-semibold' => ! $grouped,
                'font-medium' => $grouped,
            ])
        >
            {{ $slot }}
        </span>

        @if (filled($badge))
            <span
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-show="$store.sidebar.isOpen"
                    x-transition:enter="lg:transition lg:delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                @endif
            >
                <x-filament::badge :color="$badgeColor">
                    {{ $badge }}
                </x-filament::badge>
            </span>
        @endif
    </a>

    @if (($active || $activeChildItems) && $childItems)
        <ul class="fi-sidebar-sub-group-items flex flex-col gap-y-1">
            @foreach ($childItems as $childItem)
                <x-filament-panels::sidebar.item
                    :active-child-items="$childItem->isChildItemsActive()"
                    :active-icon="$childItem->getActiveIcon()"
                    :active="$childItem->isActive()"
                    :badge-color="$childItem->getBadgeColor()"
                    :badge="$childItem->getBadge()"
                    grouped
                    sub-grouped
                    :icon="$childItem->getIcon()"
                    :last="$loop->last"
                    :url="$childItem->getUrl()"
                    :should-open-url-in-new-tab="$childItem->shouldOpenUrlInNewTab()"
                >
                    {{ $childItem->getLabel() }}
                </x-filament-panels::sidebar.item>
            @endforeach
        </ul>
    @endif
</li>
