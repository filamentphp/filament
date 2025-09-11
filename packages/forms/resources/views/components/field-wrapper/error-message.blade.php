<p
    data-validation-error
    {{
        $attributes->class([
            'fi-fo-field-wrp-error-message text-danger-600 dark:text-danger-400 text-sm',
        ])
    }}
>
    {{ $slot }}
</p>
