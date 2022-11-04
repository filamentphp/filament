@props([
    'parentGroup' => null,
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
])

<li
    x-data="{ label: {{ \Illuminate\Support\Js::from((filled($parentGroup) ? "{$parentGroup}." : null) . $label) }} }"
    class="filament-sidebar-group"
    @if (filled($parentGroup))
        x-bind:class="{{ config('filament.layout.sidebar.is_collapsible_on_desktop') ? '$store.sidebar.isOpen' : 'true' }} ? 'ml-11 pr-3 pt-3' : 'hidden'"
    @endif
>
    @if ($label)
        <button
            @if ($collapsible)
                x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-show="$store.sidebar.isOpen"
            @endif
            class="flex items-center justify-between w-full"
        >
            <div @class([
                'flex items-center gap-4 text-gray-600',
                'dark:text-gray-300' => config('filament.dark_mode'),
            ])>
                @if ($icon)
                    <x-dynamic-component :component="$icon" class="ml-1 w-3 h-3 flex-shrink-0" />
                @endif

                <p class="flex-1 font-bold uppercase text-xs tracking-wider">
                    {{ $label }}
                </p>
            </div>

            @if ($collapsible)
                <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                    'w-3 h-3 text-gray-600 transition',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])" x-bind:class="$store.sidebar.groupIsCollapsed(label) || '-rotate-180'" x-cloak />
            @endif
        </button>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && {{ config('filament.layout.sidebar.is_collapsible_on_desktop') ? '$store.sidebar.isOpen' : 'true' }})"
        x-collapse.duration.200ms
        @class([
            'text-sm space-y-1 -mx-3',
            'mt-2' => $label,
        ])
    >
        @foreach ($items as $item)
            @if ($item instanceof \Filament\Navigation\NavigationItem)
                <x-filament::layouts.app.sidebar.item
                    :active="$item->isActive()"
                    :icon="$item->getIcon()"
                    :active-icon="$item->getActiveIcon()"
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
                    :parentGroup="(filled($parentGroup) ? ('$parentGroup' . '.') : null) . $label"
                />
            @endif
        @endforeach
    </ul>
</li>
