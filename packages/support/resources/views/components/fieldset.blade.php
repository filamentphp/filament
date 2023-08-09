@props([
    'label' => null,
    'isLabelHidden' => false,
])

<fieldset
    {{
        $attributes->class([
            'fi-fieldset rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10',
        ])
    }}
>
    @if (filled($label) && ! $isLabelHidden)
        <legend class="text-sm font-medium leading-6">
            {{ $label }}
        </legend>
    @endif

    {{ $slot }}
</fieldset>
