@props([
    'alpineValid' => null,
    'valid' => true,
])

@php
    $hasAlpineValidClasses = filled($alpineValid);

    $validClasses = 'text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:ring-white/20 dark:checked:bg-primary-600 dark:focus:ring-primary-600 dark:checked:focus:ring-primary-500/50';
    $invalidClasses = 'text-danger-600 ring-danger-600 focus:ring-danger-600 checked:focus:ring-danger-500/50 dark:ring-danger-400 dark:checked:bg-danger-600 dark:focus:ring-danger-400 dark:checked:focus:ring-danger-500/50';
@endphp

<input
    type="checkbox"
    @if ($hasAlpineValidClasses)
        x-bind:class="{
            @js($validClasses): {{ $alpineValid }},
            @js($invalidClasses): {{ "(! {$alpineValid})" }},
        }"
    @endif
    {{
        $attributes
            ->class([
                'fi-checkbox-input rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-current disabled:checked:text-gray-400 dark:bg-gray-900 dark:disabled:bg-gray-950 dark:disabled:checked:bg-gray-600',
                $validClasses => (! $hasAlpineValidClasses) && $valid,
                $invalidClasses => (! $hasAlpineValidClasses) && (! $valid),
            ])
    }}
/>
