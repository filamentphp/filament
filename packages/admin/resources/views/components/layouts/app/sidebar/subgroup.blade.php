@props([
    'collapsible' => true,
    'label' => null,
    'name' => null,
    'icon' => null,
])

<li x-data="{ label: {{ \Illuminate\Support\Js::from($name) }} }"
    @class(['filament-sidebar-subgroup overflow-hidden'])
>
    @if ($label)
        <button
            x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-show="$store.sidebar.isOpen"
            @endif
            @class([
                'flex items-center justify-center w-full gap-3 px-3 py-2 rounded-lg font-medium transition',
                'dark:text-gray-300 dark:hover:bg-gray-700' => config('filament.dark_mode'),
            ])
        >
            @if ($icon)
                <x-dynamic-component :component="$icon" class="h-5 w-5 shrink-0" />
            @endif

            <div @class([
                'flex flex-1 text-gray-600',
                'dark:text-gray-300' => config('filament.dark_mode'),
            ])>
                {{ $label }}
            </div>

            @if ($collapsible)
                <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                    'w-3 h-3 text-gray-600 transition',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])" x-bind:class="$store.sidebar.groupIsCollapsed(label) || '-rotate-180'" x-cloak/>
            @endif
        </button>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && {{ config('filament.layout.sidebar.is_collapsible_on_desktop') ? '$store.sidebar.isOpen' : 'true' }})"
        x-collapse.duration.200ms
        @class([
            'text-sm space-y-1 mx-4',
            'mt-2' => $label,
        ])
    >
        {{ $slot }}
    </ul>
</li>
