@props([
    'error' => false,
    'prefix' => false,
    'size' => 'md',
    'suffix' => false,
])

<select
    {{
        $attributes->class([
            'filament-select-input block text-gray-900 shadow-sm outline-none transition duration-75 focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white sm:text-sm',
            'h-9 py-1' => $size === 'sm',
            'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500' => ! $error,
            'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $error,
            'rounded-s-lg' => ! $prefix,
            'rounded-e-lg' => ! $suffix,
        ])
    }}
>
    {{ $slot }}
</select>
