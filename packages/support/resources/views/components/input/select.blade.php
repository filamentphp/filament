@props([
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

<select
    {{
        $attributes->class([
            'fi-select-input',
            'fi-select-input-has-inline-prefix' => $inlinePrefix,
        ])
    }}
>
    {{ $slot }}
</select>
