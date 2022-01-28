@props([
    'label' => null,
])

<li x-data="{ label: {{ json_encode($label) }} }" class="filament-sidebar-group">
    @if ($label)
        <button
            x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            class="flex items-center justify-between w-full"
        >
            <p class="font-bold uppercase text-gray-600 text-xs tracking-wider">
                {{ $label }}
            </p>

            <x-heroicon-o-chevron-down class="w-3 h-3 text-gray-600" x-show="$store.sidebar.groupIsCollapsed(label)" />
            <x-heroicon-o-chevron-up class="w-3 h-3 text-gray-600" x-show="! $store.sidebar.groupIsCollapsed(label)" />
        </button>
    @endif

    <ul x-show="! $store.sidebar.groupIsCollapsed(label)" x-collapse.duration.200ms @class([
        'text-sm space-y-1 -mx-3',
        'mt-2' => $label,
    ])>
        {{ $slot }}
    </ul>
</li>
