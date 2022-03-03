@props([
    'extraAttributes' => [],
    'isSortColumn' => false,
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
])

<th {{ $attributes->merge($extraAttributes)->class(['p-0 filament-tables-header-cell']) }}>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex items-center w-full px-4 py-2 whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600',
            'dark:text-gray-300' => config('tables.dark_mode'),
            'cursor-default' => ! $sortable,
            match ($alignment) {
                'left' => 'justify-start',
                'center' => 'justify-center',
                'right' => 'justify-end',
                default => null,
            },
        ])
    >
        <span>
            {{ $slot }}
        </span>

        @if ($sortable)
            <span @class([
                'relative flex items-center',
                'dark:text-gray-300' => config('tables.dark_mode'),
            ])>
                @if ($isSortColumn && $sortDirection === 'asc')
                    <x-heroicon-s-chevron-up class="w-3 h-3" />
                @else
                    <x-heroicon-s-chevron-down :class="\Illuminate\Support\Arr::toCssClasses(['w-3 h-3', 'opacity-25' => ! $isSortColumn])" />
                @endif

            </span>
        @endif
    </button>
</th>
