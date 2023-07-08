<select
    {{
        $attributes->class([
            'filament-select-input block w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6',
        ])
    }}
>
    {{ $slot }}
</select>
