@props([
    'label' => null,
    'labelHidden' => false,
])

<fieldset
    {{
        $attributes->class([
            'fi-fieldset',
            'fi-fieldset-label-hidden' => $labelHidden,
        ])
    }}
>
    @if (filled($label))
        <legend>
            {{ $label }}
        </legend>
    @endif

    {{ $slot }}
</fieldset>
