@props([
    'collapsible' => true,
    'icon' => null,
    'label' => null,
])

<li x-data="{ label: {{ \Illuminate\Support\Js::from($label) }} }" class="filament-sidebar-group">
    @if ($label)
        <button
            @if ($collapsible)
                x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop)
                x-show="$store.sidebar.isOpen"
            @endif
            class="flex items-center justify-between w-full"
        >
            <div class="flex items-center gap-4">
                @if ($icon)
                    <x-filament::icon
                        :name="$icon"
                        alias="app::sidebar.group"
                        color="text-gray-600 dark:text-gray-300"
                        size="h-3 w-3"
                        class="ml-1 flex-shrink-0"
                    />
                @endif

                <p class="flex-1 font-bold uppercase text-xs tracking-wider">
                    {{ $label }}
                </p>
            </div>

            @if ($collapsible)
                <x-filament::icon
                    name="heroicon-m-chevron-down"
                    alias="app::sidebar.group.trigger"
                    size="h-3 w-3"
                    class="text-gray-600 transition dark:text-gray-300"
                    x-bind:class="$store.sidebar.groupIsCollapsed(label) || '-rotate-180'"
                    x-cloak
                />
            @endif
        </button>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && {{ \Filament\Navigation\Sidebar::$isCollapsibleOnDesktop ? '$store.sidebar.isOpen' : 'true' }})"
        x-collapse.duration.200ms
        @class([
            'text-sm space-y-1 -mx-3',
            'mt-2' => $label,
        ])
    >
        {{ $slot }}
    </ul>
</li>
