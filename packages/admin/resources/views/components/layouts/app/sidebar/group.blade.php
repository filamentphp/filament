@props([
    'collapsible' => true,
    'collapsed' => false,
    'icon' => null,
    'label' => null,
])

<li x-data="{ label: {{ \Illuminate\Support\Js::from($label) }} }" @if($collapsed)x-init="$store.sidebar.collapseGroup(label)"@endif class="filament-sidebar-group">
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
            <p @class([
                'flex items-center justify-center gap-3 font-bold uppercase text-gray-600 text-xs tracking-wider',
                'dark:text-gray-300' => config('filament.dark_mode'),
            ])>
                @if($icon)
                    <x-dynamic-component :component="$icon" class="h-5 w-5 shrink-0" />
                @endif

                {{ $label }}
            </p>

            @if ($collapsible)
                <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                    'w-3 h-3 text-gray-600 transition-all',
                    'dark:text-gray-300' =>  config('filament.dark_mode'),
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
        {{ $slot }}
    </ul>
</li>
