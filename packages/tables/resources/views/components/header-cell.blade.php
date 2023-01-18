@props([
    'activelySorted' => false,
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
    'wrap' => false,
])

<th {{ $attributes->class(['filament-tables-header-cell p-0']) }}>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex items-center w-full px-4 py-2 space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600 dark:text-gray-300',
            'cursor-default' => ! $sortable,
            'whitespace-nowrap' => ! $wrap,
            'whitespace-normal' => $wrap,
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
                alias="$activelySorted && $sortDirection === 'asc' ? 'filament-tables::header-cell.sort-asc' : 'filament-tables::header-cell.sort-desc'"
                color="dark:text-gray-300"
                size="h-5 w-5"
                :class="[
                    'filament-tables-header-cell-sort-icon',
                    'opacity-25' => ! $activelySorted,
                ]"
            />
        @endif
    </button>
</th>
