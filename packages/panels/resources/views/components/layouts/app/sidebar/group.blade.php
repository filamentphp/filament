@props([
    'collapsible' => true,
    'hasItemIcons' => false,
    'icon' => null,
    'items' => [],
    'label' => null,
])

<li
    x-data="{ label: @js($label) }"
    @class([
        'filament-sidebar-group grid gap-y-1',
    ])
>
    @if ($label)
        <div
            @if ($collapsible)
                x-on:click="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="lg:transition delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            @class([
                'flex items-center gap-x-3 px-3 py-2',
                'cursor-pointer' => $collapsible,
            ])
        >
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    alias="panels::sidebar.group"
                    color="text-gray-600 dark:text-gray-400"
                    size="h-6 w-6"
                />
            @endif

            <span
                class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
                {{ $label }}
            </span>

            @if ($collapsible)
                <x-filament::icon-button
                    color="gray"
                    icon="heroicon-m-chevron-down"
                    icon-alias="panels::sidebar.group.collapse"
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup(label)"
                    x-bind:class="{ 'rotate-180': ! $store.sidebar.groupIsCollapsed(label) }"
                    class="-my-2.5 -me-2.5"
                />
            @endif
        </div>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && ($store.sidebar.isOpen || @js(! filament()->isSidebarCollapsibleOnDesktop())))"
        @if (filament()->isSidebarCollapsibleOnDesktop())
            x-transition:enter="lg:transition delay-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        @endif
        x-collapse.duration.200ms
        @class([
            'grid gap-y-1',
        ])
    >
        @foreach ($items as $item)
            @if ($item->isVisible())
                <x-filament::layouts.app.sidebar.item
                    :active="$item->isActive()"
                    :has-grouped-border="! $hasItemIcons"
                    :icon="$item->getIcon()"
                    :first="$loop->first"
                    :last="$loop->last"
                    :active-icon="$item->getActiveIcon()"
                    :url="$item->getUrl()"
                    :badge="$item->getBadge()"
                    :badge-color="$item->getBadgeColor()"
                    :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                >
                    {{ $item->getLabel() }}
                </x-filament::layouts.app.sidebar.item>
            @endif
        @endforeach
    </ul>
</li>
