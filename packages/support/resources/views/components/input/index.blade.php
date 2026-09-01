@props([
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

<input
    {{
        $attributes->class([
            'fi-input',
            'fi-input-has-inline-prefix' => $inlinePrefix,
            'fi-input-has-inline-suffix' => $inlineSuffix,
        ])
    }}
/>
