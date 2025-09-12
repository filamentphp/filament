@props([
    'active' => false,
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
    'sidebarCollapsible' => true,
    'subNavigation' => false,
])

@php
    $sidebarCollapsible = $sidebarCollapsible && filament()->isSidebarCollapsibleOnDesktop();
    $hasDropdown = filled($label) && filled($icon) && $sidebarCollapsible;
@endphp

<li
    x-data="{ label: @js($subNavigation ? "sub_navigation_{$label}" : $label) }"
    data-group-label="{{ $subNavigation ? "sub_navigation_{$label}" : $label }}"
    {{
        $attributes->class([
            'fi-sidebar-group flex flex-col gap-y-1',
            'fi-active' => $active,
        ])
    }}
>
    @if ($label)
        <div
            @if ($collapsible)
                x-on:click="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if ($sidebarCollapsible)
                x-show="$store.sidebar.isOpen"
                x-transition:enter="delay-100 lg:transition"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
            @class([
                'fi-sidebar-group-button flex items-center gap-x-3 px-2 py-2',
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
                class="fi-sidebar-group-label flex-1 text-sm font-medium leading-6 text-gray-500 dark:text-gray-400"
            >
                {{ $label }}
            </span>

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

    @if ($hasDropdown)
        <x-filament::dropdown
            :placement="(__('filament-panels::layout.direction') === 'rtl') ? 'left-start' : 'right-start'"
            teleport
            x-show="! $store.sidebar.isOpen"
        >
            <x-slot name="trigger">
                <button
                    x-data="{ tooltip: false }"
                    x-effect="
                        tooltip = $store.sidebar.isOpen
                            ? false
                            : {
                                  content: @js($label),
                                  placement: document.dir === 'rtl' ? 'left' : 'right',
                                  theme: $store.theme,
                              }
                    "
                    x-tooltip.html="tooltip"
                    class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
                >
                    <x-filament::icon
                        :icon="$icon"
                        @class([
                            'h-6 w-6',
                            'text-gray-400 dark:text-gray-500' => ! $active,
                            'text-primary-600 dark:text-primary-400' => $active,
                        ])
                    />
                </button>
            </x-slot>

            @php
                $lists = [];

                foreach ($items as $item) {
                    if ($childItems = $item->getChildItems()) {
                        $lists[] = [
                            $item,
                            ...$childItems,
                        ];
                        $lists[] = [];

                        continue;
                    }

                    if (empty($lists)) {
                        $lists[] = [$item];

                        continue;
                    }

                    $lists[count($lists) - 1][] = $item;
                }

                if (empty($lists[count($lists) - 1])) {
                    array_pop($lists);
                }
            @endphp

            @if (filled($label))
                <x-filament::dropdown.header>
                    {{ $label }}
                </x-filament::dropdown.header>
            @endif

            @foreach ($lists as $list)
                <x-filament::dropdown.list>
                    @foreach ($list as $item)
                        @php
                            $itemIsActive = $item->isActive();
                        @endphp

                        <x-filament::dropdown.list.item
                            :badge="$item->getBadge()"
                            :badge-color="$item->getBadgeColor()"
                            :badge-tooltip="$item->getBadgeTooltip()"
                            :color="$itemIsActive ? 'primary' : 'gray'"
                            :href="$item->getUrl()"
                            :icon="$itemIsActive ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon()"
                            tag="a"
                            :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                        >
                            {{ $item->getLabel() }}
                        </x-filament::dropdown.list.item>
                    @endforeach
                </x-filament::dropdown.list>
            @endforeach
        </x-filament::dropdown>
    @endif

    <ul
        @if (filled($label))
            @if ($sidebarCollapsible)
                x-show="$store.sidebar.isOpen ? ! $store.sidebar.groupIsCollapsed(label) : ! @js($hasDropdown)"
            @else
                x-show="! $store.sidebar.groupIsCollapsed(label)"
            @endif
            x-collapse.duration.200ms
        @endif
        @if ($sidebarCollapsible)
            x-transition:enter="delay-100 lg:transition"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        @endif
        class="fi-sidebar-group-items flex flex-col gap-y-1"
    >
        @foreach ($items as $item)
            @php
                $itemIcon = $item->getIcon();
                $itemActiveIcon = $item->getActiveIcon();

                if ($icon) {
                    if ($hasDropdown || (blank($itemIcon) && blank($itemActiveIcon))) {
                        $itemIcon = null;
                        $itemActiveIcon = null;
                    } else {
                        throw new \Exception('Navigation group [' . $label . '] has an icon but one or more of its items also have icons. Either the group or its items can have icons, but not both. This is to ensure a proper user experience.');
                    }
                }
            @endphp

            <x-filament-panels::sidebar.item
                :active="$item->isActive()"
                :active-child-items="$item->isChildItemsActive()"
                :active-icon="$itemActiveIcon"
                :badge="$item->getBadge()"
                :badge-color="$item->getBadgeColor()"
                :badge-tooltip="$item->getBadgeTooltip()"
                :child-items="$item->getChildItems()"
                :first="$loop->first"
                :grouped="filled($label)"
                :icon="$itemIcon"
                :last="$loop->last"
                :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                :sidebar-collapsible="$sidebarCollapsible"
                :url="$item->getUrl()"
            >
                {{ $item->getLabel() }}

                @if ($itemIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                    <x-slot name="icon">
                        {{ $itemIcon }}
                    </x-slot>
                @endif

                @if ($itemActiveIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                    <x-slot name="activeIcon">
                        {{ $itemActiveIcon }}
                    </x-slot>
                @endif
            </x-filament-panels::sidebar.item>
        @endforeach
    </ul>
</li>
