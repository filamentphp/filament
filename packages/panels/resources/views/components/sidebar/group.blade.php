@props([
    'active' => false,
    'activeIcon' => null,
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
    'url' => null,
    'shouldOpenUrlInNewTab' => false,
])

<li
    x-data="{ label: @js($label) }"
    data-group-label="{{ $label }}"
    @class([
        'fi-sidebar-group flex flex-col gap-y-1',
        'fi-active fi-sidebar-group-active' => $active,
    ])
>
    @if ($label)
        <div
            @if ($collapsible && ! $url)
                x-on:click="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="delay-100 lg:transition"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            @class([
                'flex items-center gap-x-3 rounded-lg px-2 py-2',
                'transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5' => (bool) $url,
                'bg-gray-100 dark:bg-white/5' => $active,
                'cursor-pointer' => $collapsible,
            ])
        >
            @if ($icon)
                <x-filament::icon
                    :icon="($active && $activeIcon) ? $activeIcon : $icon"
                    @class([
                        'fi-sidebar-group-icon h-6 w-6',
                        'text-gray-400 dark:text-gray-500' => ! $active,
                        'text-primary-600 dark:text-primary-400' => $active,
                    ])
                />
            @endif

            <a
                @if ($url)
                    {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
                @endif
                @class([
                    'fi-sidebar-group-label flex-1 text-sm font-semibold leading-6',
                    'text-gray-700 dark:text-gray-200' => ! $active,
                    'text-primary-600 dark:text-primary-400' => $active,
                ])
            >
                {{ $label }}
            </a>

            @if ($collapsible)
                <x-filament::icon-button
                    color="gray"
                    icon="heroicon-m-chevron-up"
                    icon-alias="panels::sidebar.group.collapse-button"
                    :label="$label"
                    x-bind:aria-expanded="! $store.sidebar.groupIsCollapsed(label)"
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup(label)"
                    class="fi-sidebar-group-collapse-button"
                    x-bind:class="{ '-rotate-180': $store.sidebar.groupIsCollapsed(label) }"
                />
            @endif
        </div>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && ($store.sidebar.isOpen || @js(! filament()->isSidebarCollapsibleOnDesktop())))"
        @if (filament()->isSidebarCollapsibleOnDesktop())
            x-transition:enter="delay-100 lg:transition"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        @endif
        x-collapse.duration.200ms
        class="fi-sidebar-group-items flex flex-col gap-y-1"
    >
        @foreach ($items as $item)
            <x-filament-panels::sidebar.item
                :active-icon="$item->getActiveIcon()"
                :active="$item->isActive()"
                :badge-color="$item->getBadgeColor()"
                :badge="$item->getBadge()"
                :first="$loop->first"
                :grouped="filled($label)"
                :icon="$item->getIcon()"
                :last="$loop->last"
                :url="$item->getUrl()"
                :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
            >
                {{ $item->getLabel() }}
            </x-filament-panels::sidebar.item>
        @endforeach
    </ul>
</li>
