@props([
    'extraAttributes' => [],
    'isSortColumn' => false,
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
])

<th
    {{
        $attributes
            ->merge($extraAttributes)
            ->class(['filament-tables-header-cell p-0'])
    }}
>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex w-full items-center gap-x-1 whitespace-nowrap px-4 py-2 text-sm font-medium text-gray-600',
            'dark:text-gray-300' => config('tables.dark_mode'),
            'cursor-default' => ! $sortable,
            match ($alignment) {
                'start' => 'justify-start',
                'center' => 'justify-center',
                'end' => 'justify-end',
                'left' => 'justify-start rtl:flex-row-reverse',
                'center' => 'justify-center',
                'right' => 'justify-end rtl:flex-row-reverse',
                default => null,
            },
        ])
    >
        @if ($sortable)
            <span class="sr-only">
                {{ __('tables::table.sorting.fields.column.label') }}
            </span>
        @endif

        <span>
            {{ $slot }}
        </span>

        @if ($sortable)
            <span class="sr-only">
                {{ $sortDirection === 'asc' ? __('tables::table.sorting.fields.direction.options.desc') : __('tables::table.sorting.fields.direction.options.asc') }}
            </span>
        @endif

        @if ($sortable)
            <x-dynamic-component
                :component="$isSortColumn && $sortDirection === 'asc' ? 'heroicon-s-chevron-up' : 'heroicon-s-chevron-down'"
                :class="
                    \Illuminate\Support\Arr::toCssClasses([
                        'filament-tables-header-cell-sort-icon h-3 w-3',
                        'dark:text-gray-300' => config('tables.dark_mode'),
                        'opacity-25' => ! $isSortColumn,
                    ])
                "
            />
        @endif
    </button>
</th>
