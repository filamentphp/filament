@props([
    'extraAttributes' => [],
    'isSortColumn' => false,
    'name',
    'sortable' => false,
    'sortDirection',
])

<th {{ $attributes->merge($extraAttributes)->class([
    'px-4 py-2 filament-tables-header-cell',
    'dark:bg-gray-800' => config('tables.dark_mode'),
]) }}>
    <button
        @if ($sortable)
            wire:click="sortTable('{{ $name }}')"
        @endif
        type="button"
        @class([
            'flex items-center whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600',
            'dark:text-gray-300' => config('tables.dark_mode'),
            'cursor-default' => ! $sortable,
        ])
    >
        <span>
            {{ $slot }}
        </span>

        @if($sortable)
        <span @class([
                'relative flex-col items-center',
                'dark:text-gray-300' => config('tables.dark_mode'),
            ])>

            <x-heroicon-s-chevron-up
                class="w-3 h-3 {{ ($isSortColumn && $sortDirection === 'asc') ? null : 'text-gray-400' }}"
            />

            <x-heroicon-s-chevron-down
                class="w-3 h-3 {{ ($isSortColumn && $sortDirection === 'desc') ? null : 'text-gray-400' }}"
            />
        </span>
        @endif
    </button>
</th>
