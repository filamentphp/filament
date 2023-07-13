@props([
    'canSelectPlaceholder' => false,
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

@php
    $colorClasses = 'text-gray-950 dark:text-white';
@endphp

<select
    @if ($canSelectPlaceholder)
        x-data="{ isPlaceholderSelected: true }"
        x-init="isPlaceholderSelected = $el.value === ''"
        x-on:change="isPlaceholderSelected = $event.target.value === ''"
        x-bind:class="
            isPlaceholderSelected
                ? 'text-gray-400 dark:text-gray-500'
                : '{{ $colorClasses }}'
        "
    @endif
    {{
        $attributes->class([
            'fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 text-base transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6',
            'ps-0' => $inlinePrefix,
            'ps-3' => ! $inlinePrefix,
            $colorClasses => ! $canSelectPlaceholder,
        ])
    }}
>
    {{ $slot }}
</select>
