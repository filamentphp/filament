@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'activelySorted' => false,
    'alignment' => Alignment::Start,
    'name',
    'sortable' => false,
    'sortDirection',
    'wrap' => false,
])

@php
    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<th
    @if ($activelySorted)
        aria-sort="{{ $sortDirection === 'asc' ? 'ascending' : 'descending' }}"
    @endif
    {{ $attributes->class(['fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6']) }}
>
    <span
        @if ($sortable)
            aria-label="{{ trim(strip_tags($slot)) }}"
            role="button"
            tabindex="0"
            wire:click="sortTable('{{ $name }}')"
            x-on:keydown.enter.prevent.stop="$wire.sortTable('{{ $name }}')"
            x-on:keydown.space.prevent.stop="$wire.sortTable('{{ $name }}')"
        @endif
        @class([
            'group flex w-full items-center gap-x-1',
            'cursor-pointer' => $sortable,
            'whitespace-nowrap' => ! $wrap,
            'whitespace-normal' => $wrap,
            match ($alignment) {
                Alignment::Start => 'justify-start',
                Alignment::Center => 'justify-center',
                Alignment::End => 'justify-end',
                Alignment::Left => 'justify-start rtl:flex-row-reverse',
                Alignment::Right => 'justify-end rtl:flex-row-reverse',
                Alignment::Justify, Alignment::Between => 'justify-between',
                default => $alignment,
            },
        ])
    >
        <span
            class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white"
        >
            {{ $slot }}
        </span>

        @if ($sortable)
            <x-filament::icon
                :alias="
                    match (true) {
                        $activelySorted && ($sortDirection === 'asc') => 'tables::header-cell.sort-asc-button',
                        $activelySorted && ($sortDirection === 'desc') => 'tables::header-cell.sort-desc-button',
                        default => 'tables::header-cell.sort-button',
                    }
                "
                :icon="$activelySorted && $sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                @class([
                    'fi-ta-header-cell-sort-icon h-5 w-5 shrink-0 transition duration-75',
                    'text-gray-950 dark:text-white' => $activelySorted,
                    'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 group-focus-visible:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus-visible:text-gray-400' => ! $activelySorted,
                ])
            />
        @endif
    </span>
</th>
