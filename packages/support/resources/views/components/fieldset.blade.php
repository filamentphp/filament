@props([
    'label' => null,
])

<fieldset
    {{
        $attributes->class([
            'fi-fieldset rounded-xl p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10',
        ])
    }}
>
    @if (filled($label))
        <legend class="text-sm font-medium leading-6 bg-gray-50 dark:bg-gray-950 px-2 -mx-2">
            {{ $label }}
        </legend>
    @endif

    {{ $slot }}
</fieldset>
