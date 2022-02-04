<th {{ $attributes->class(['w-4 px-4 whitespace-nowrap dark:bg-dark-800', 'filament-tables-checkbox-cell']) }}>
    <input
        {{ $checkbox->attributes->class(['block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-dark-700 dark:border-dark-600 dark:checked:bg-primary-500']) }}
        type="checkbox"
    />
</th>
