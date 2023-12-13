@props([
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

<select
    {{
        $attributes->class([
            'fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:dark:bg-gray-900 [&_option]:bg-white [&_option]:dark:bg-gray-900',
            'ps-0' => $inlinePrefix,
            'ps-3' => ! $inlinePrefix,
        ])
    }}
>
    {{ $slot }}
</select>
