<p
    data-validation-error
    {{ $attributes->class([
        'filament-forms-field-wrapper-error-message text-sm text-danger-600',
        'dark:text-danger-400' => config('forms.dark_mode'),
    ]) }}
>
    {{ $slot }}
</p>
