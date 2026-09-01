@props([
    'alpineValid' => null,
    'valid' => true,
])

@php
    $hasAlpineValidClasses = filled($alpineValid);
@endphp

<input
    type="checkbox"
    @if ($hasAlpineValidClasses)
        x-bind:class="{
            'fi-valid': {{ $alpineValid }},
            'fi-invalid': {{ "(! {$alpineValid})" }},
        }"
    @endif
    {{
        $attributes
            ->class([
                'fi-checkbox-input',
                'fi-valid' => (! $hasAlpineValidClasses) && $valid,
                'fi-invalid' => (! $hasAlpineValidClasses) && (! $valid),
            ])
    }}
/>
