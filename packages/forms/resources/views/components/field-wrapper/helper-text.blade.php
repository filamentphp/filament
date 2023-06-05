<div
    {{
        $attributes->class([
            'filament-forms-field-wrapper-helper-text text-sm text-gray-600',
            'dark:text-gray-300' => config('forms.dark_mode'),
        ])
    }}
>
    {{ $slot }}
</div>
