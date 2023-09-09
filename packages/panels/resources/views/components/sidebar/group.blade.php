@props([
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
])

<li
    x-data="{ label: @js($label) }"
    data-group-label="{{ $label }}"
    class="fi-sidebar-group flex flex-col gap-y-1"
>
    @if ($label)
        <div
            @if ($collapsible)
                x-on:click="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="delay-100 lg:transition"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            @class([
                'flex items-center gap-x-3 px-2 py-2',
                'cursor-pointer' => $collapsible,
            ])
        >
            @if ($icon)
                <x-filament::icon
                    :icon="$icon"
                    class="fi-sidebar-group-icon h-6 w-6 text-gray-400 dark:text-gray-500"
                />
            @endif

            <span
                class="fi-sidebar-group-label flex-1 text-sm font-semibold text-gray-700 dark:text-gray-200"
            >
                {{ $label }}
            </span>

            @if ($collapsible)
                <x-filament::icon-button
                    color="gray"
                    icon="heroicon-m-chevron-up"
                    icon-alias="panels::sidebar.group.collapse-button"
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup(label)"
                    x-bind:class="{ 'rotate-180': $store.sidebar.groupIsCollapsed(label) }"
                    class="fi-sidebar-group-collapse-button -my-2 -me-2"
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
                :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                :url="$item->getUrl()"
            >
                {{ $item->getLabel() }}
            </x-filament-panels::sidebar.item>
        @endforeach
    </ul>
</li>
