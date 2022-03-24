@props([
    'label' => null,
])

<li x-data="{ label: {{ json_encode($label) }} }" class="filament-sidebar-group">
    @if ($label)
        <button
            x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            class="flex items-center justify-between w-full"
        >
            <p 
                @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                    x-show="$store.sidebar.isOpen"
                @endif
                @class([
                    'font-bold uppercase text-gray-600 text-xs tracking-wider',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])
            >
                {{ $label }}
            </p>

            <div class="ml-3 rtl:ml-auto rtl:mr-3">
                <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                    'w-3 h-3 text-gray-600',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])" x-show="$store.sidebar.groupIsCollapsed(label)" x-cloak />

                <x-heroicon-o-chevron-up :class="\Illuminate\Support\Arr::toCssClasses([
                    'w-3 h-3 text-gray-600',
                    'dark:text-gray-300' => config('filament.dark_mode'),
                ])" x-show="! $store.sidebar.groupIsCollapsed(label)" />
            </div>
        </button>
    @endif

    <ul x-show="! $store.sidebar.groupIsCollapsed(label)" x-collapse.duration.200ms @class([
        'text-sm space-y-1 -mx-3',
        'mt-2' => $label,
    ])>
        {{ $slot }}
    </ul>
</li>
