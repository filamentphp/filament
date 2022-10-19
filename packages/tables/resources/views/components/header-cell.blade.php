@props([
    'activelySorted' => false,
    'extraAttributes' => [],
    'name',
    'sortable' => false,
    'sortDirection',
    'alignment' => null,
])

<th {{ $attributes->merge($extraAttributes)->class(['filament-tables-header-cell p-0']) }}>
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
            @svg(
                $activelySorted && $sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down',
                \Illuminate\Support\Arr::toCssClasses([
                    'filament-tables-header-cell-sort-icon h-3 w-3',
                    'dark:text-gray-300' => config('tables.dark_mode'),
                    'opacity-25' => ! $activelySorted,
                ]),
            )
        @endif
    </button>
</th>
