@props([
    'options',
])

<div class="flex items-center space-x-2 filament-tables-pagination-records-per-page-selector rtl:space-x-reverse">
    <select wire:model="tableRecordsPerPage" id="tableRecordsPerPageSelect" @class([
        'h-8 text-sm pr-8 leading-none transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
        'dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500' => config('tables.dark_mode'),
    ])>
        @foreach ($options as $option)
            <option value="{{ $option }}">{{ $option === -1 ? __('tables::table.pagination.fields.records_per_page.options.all') : $option }}</option>
        @endforeach
    </select>

    <label for="tableRecordsPerPageSelect" @class([
        'text-sm font-medium',
        'dark:text-white' => config('tables.dark_mode'),
    ])>
        {{ __('tables::table.pagination.fields.records_per_page.label') }}
    </label>
</div>
