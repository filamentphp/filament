@props([
    'collapsible' => true,
    'icon' => null,
    'label' => null,
    'items' => [],
    'child' => false,
])

<li x-data="{ label: {{ \Illuminate\Support\Js::from($label) }} }" class="filament-sidebar-group">
    @if ($label)
        <button
            @if ($collapsible)
                x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-show="$store.sidebar.isOpen"
            @endif
            class="w-full px-3"
        >
            <div class="flex items-center justify-between w-full py-2">
                <div @class([
                    'flex items-center gap-4 text-gray-600',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])>
                    @if ($icon)
                        <x-dynamic-component :component="$icon" class="w-5 h-5 flex-shrink-0" />
                    @endif

                    <p class="flex-1 font-bold uppercase tracking-wider text-xs">
                        {{ $label }}
                    </p>
                </div>

                @if ($collapsible)
                    <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                        'w-3 h-3 text-gray-600 transition',
                        'dark:text-gray-300' => config('filament.dark_mode'),
                    ])" x-bind:class="$store.sidebar.groupIsCollapsed(label) || '-rotate-180'" x-cloak />
                @endif
            </div>

            @if ($child && $collapsible)
                <span class="block border-b border-gray-100 flex"></span>
            @endif
        </button>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && {{ config('filament.layout.sidebar.is_collapsible_on_desktop') ? '$store.sidebar.isOpen' : 'true' }})"
        x-collapse.duration.200ms
        @class([
            'text-sm space-y-1 -mx-3',
            'mt-2' => $label,
            'pl-3'  => $child
        ])
    >
        @foreach ($items as $item)
            @if ($item instanceof \Filament\Navigation\NavigationItem)
                <x-filament::layouts.app.sidebar.item
                    :active="$item->isActive()"
                    :icon="$item->getIcon()"
                    :url="$item->getUrl()"
                    :badge="$item->getBadge()"
                    :badgeColor="$item->getBadgeColor()"
                    :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                >
                    {{ $item->getLabel() }}
                </x-filament::layouts.app.sidebar.item>
            @elseif ($item instanceof \Filament\Navigation\NavigationGroup)
                <x-filament::layouts.app.sidebar.group
                    :label="$item->getLabel()"
                    :icon="$item->getIcon()"
                    :collapsible="$item->isCollapsible()"
                    :items="$item->getItems()"
                    :child="true"
                />
            @endif
        @endforeach
    </ul>
</li>
