@props([
    'options',
])

<div class="flex items-center space-x-2 filament-tables-pagination-records-per-page-selector rtl:space-x-reverse">
    <label>
        <select
            class="h-8 text-sm pe-8 leading-none transition duration-75 border-gray-300 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500"
            wire:model="tableRecordsPerPage"
        >
            @foreach ($options as $option)
                <option value="{{ $option }}">{{ $option === 'all' ? __('filament-tables::table.pagination.fields.records_per_page.options.all') : $option }}</option>
            @endforeach
        </select>

        <span class="text-sm font-medium dark:text-white">
            {{ __('filament-tables::table.pagination.fields.records_per_page.label') }}
        </span>
    </label>
</div>
