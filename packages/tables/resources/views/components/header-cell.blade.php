@props([
    'activelySorted' => false,
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
])

<th {{ $attributes->class(['filament-tables-header-cell p-0']) }}>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex items-center w-full px-4 py-2 whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600 dark:text-gray-300',
            'cursor-default' => ! $sortable,
            match ($alignment) {
                'center' => 'justify-center',
                'end' => 'justify-end',
                'left' => 'justify-start rtl:flex-row-reverse',
                'right' => 'justify-end rtl:flex-row-reverse',
                'start' => 'justify-start',
                default => null,
            },
        ])
    >
        <span>
            {{ $slot }}
        </span>

        @if ($sortable)
            <x-filament::icon
                :name="$activelySorted && $sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                alias="filament-tables::header-cell.sort"
                color="dark:text-gray-300"
                size="h-3 w-3"
                :class="[
                    'filament-tables-header-cell-sort-icon',
                    'opacity-25' => ! $activelySorted,
                ]"
            />
        @endif
    </button>
</th>
