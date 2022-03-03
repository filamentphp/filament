<th {{ $attributes->class(['w-4 px-4 whitespace-nowrap filament-tables-checkbox-cell']) }}>
    <input
        {{ $checkbox->attributes->class([
            'block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50',
            'dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-primary-500' => config('tables.dark_mode'),
        ]) }}
        type="checkbox"
    />
</th>
