@props([
    'activelySorted' => false,
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
    'wrap' => false,
])

<th {{ $attributes->class(['fi-ta-header-cell p-0']) }}>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300',
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
        @if ($sortable)
            <span class="sr-only">
                {{ __('filament-tables::table.sorting.fields.column.label') }}
            </span>
        @endif

        <span>
            {{ $slot }}
        </span>

        @if ($sortable)
            <x-filament::icon
                :alias="$activelySorted && $sortDirection === 'asc' ? 'tables::header-cell.sort-asc' : 'tables::header-cell.sort-desc'"
                :icon="$activelySorted && $sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                @class([
                    'fi-ta-header-cell-sort-icon h-5 w-5 dark:text-gray-300',
                    'opacity-25' => ! $activelySorted,
                ])
            />

            <span class="sr-only">
                {{ $sortDirection === 'asc' ? __('filament-tables::table.sorting.fields.direction.options.desc') : __('filament-tables::table.sorting.fields.direction.options.asc') }}
            </span>
        @endif
    </button>
</th>
