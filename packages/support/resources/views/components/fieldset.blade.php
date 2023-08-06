@props([
    'label' => null,
])

<fieldset
    {{
        $attributes->class([
            'fi-fieldset rounded-xl p-6 shadow-sm border border-gray-200 dark:border-white/10',
        ])
    }}
>
    @if (filled($label))
        <legend class="text-sm font-medium leading-6">
            {{ $label }}
        </legend>
    @endif

    {{ $slot }}
</fieldset>
