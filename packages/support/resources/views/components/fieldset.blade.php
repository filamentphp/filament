@props([
    'contained' => true,
    'label' => null,
    'labelHidden' => false,
    'required' => false,
])

<fieldset
    {{
        $attributes->class([
            'fi-fieldset',
            'fi-fieldset-label-hidden' => $labelHidden,
            'fi-fieldset-not-contained' => ! $contained,
        ])
    }}
>
    @if (filled($label))
        <legend>
            {{ $label }}@if ($required)<sup class="fi-fieldset-label-required-mark">*</sup>
            @endif
        </legend>
    @endif

    {{ $slot }}
</fieldset>
