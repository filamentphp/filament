@props([
    'darkMode' => false,
])

<div
    aria-hidden="true"
    {{
        $attributes->class([
            'filament-hr border-t',
            'dark:border-gray-700' => $darkMode,
        ])
    }}
></div>
