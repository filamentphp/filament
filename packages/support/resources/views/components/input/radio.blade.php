@props([
    'valid' => true,
])

<input
    type="radio"
    {{
        $attributes
            ->class([
                'fi-radio-input',
                'fi-invalid' => ! $valid,
            ])
    }}
/>
