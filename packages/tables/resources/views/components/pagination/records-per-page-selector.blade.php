@props([
    'options',
])

<div
    class="filament-tables-pagination-records-per-page-selector flex items-center space-x-2 rtl:space-x-reverse"
>
    <label>
        <select
            class="h-8 rounded-lg border-gray-300 pe-8 text-sm leading-none shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
            wire:model.live="tableRecordsPerPage"
        >
            @foreach ($options as $option)
                <option value="{{ $option }}">
                    {{ $option === 'all' ? __('filament-tables::table.pagination.fields.records_per_page.options.all') : $option }}
                </option>
            @endforeach
        </select>

        <span class="text-sm font-medium dark:text-white">
            {{ __('filament-tables::table.pagination.fields.records_per_page.label') }}
        </span>
    </label>
</div>
