@props([
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

<input
    {{
        $attributes->class([
            'fi-input block w-full border-none py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus-visible:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6',
            // A fully transparent white background color is used
            // instead of transparent to fix a Safari bug
            // where the date/time input "placeholder" colors too dark.
            //
            // https://github.com/filamentphp/filament/issues/7087
            'bg-white/0',
            'ps-0' => $inlinePrefix,
            'ps-3' => ! $inlinePrefix,
            'pe-0' => $inlineSuffix,
            'pe-3' => ! $inlineSuffix,
        ])
    }}
/>
