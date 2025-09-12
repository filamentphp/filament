@props([
    'valid' => true,
])

<input
    type="radio"
    {{
        $attributes
            ->class([
                'fi-radio-input border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-gray-400 disabled:checked:text-gray-400 dark:bg-white/5 dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600',
                'text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:text-primary-500 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50 dark:disabled:ring-white/10' => $valid,
                'fi-invalid text-danger-600 ring-danger-600 focus:ring-danger-600 checked:focus:ring-danger-500/50 dark:text-danger-500 dark:ring-danger-500 dark:checked:bg-danger-500 dark:focus:ring-danger-500 dark:checked:focus:ring-danger-400/50' => ! $valid,
            ])
    }}
/>
