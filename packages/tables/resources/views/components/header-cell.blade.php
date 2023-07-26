@props([
    'activelySorted' => false,
    'alignment' => null,
    'name',
    'sortable' => false,
    'sortDirection',
    'wrap' => false,
])

<th
    {{
        $attributes
            ->class(['fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6'])
    }}
>
    <button
        type="button"
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        @class([
            'group flex w-full items-center gap-x-1',
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

        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $slot }}
        </span>

        @if ($sortable)
            <x-filament::icon
                :alias="$activelySorted && $sortDirection === 'asc' ? 'tables::header-cell.sort-asc' : 'tables::header-cell.sort-desc'"
                :icon="$activelySorted && $sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                @class([
                    'fi-ta-header-cell-sort-icon h-5 w-5 transition duration-75',
                    'text-gray-950 dark:text-white' => $activelySorted,
                    'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 group-focus:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus:text-gray-400' => ! $activelySorted,
                ])
            />

            <span class="sr-only">
                {{ $sortDirection === 'asc' ? __('filament-tables::table.sorting.fields.direction.options.desc') : __('filament-tables::table.sorting.fields.direction.options.asc') }}
            </span>
        @endif
    </button>
</th>
