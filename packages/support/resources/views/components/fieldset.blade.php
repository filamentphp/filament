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
            {{ $label }}@if ($required)<sup aria-hidden="true" class="fi-fieldset-label-required-mark">*</sup>
                <span class="fi-sr-only">
                    {{ __('filament::components/required-mark.label') }}
                </span>
            @endif
        </legend>
    @endif

    {{ $slot }}
</fieldset>
