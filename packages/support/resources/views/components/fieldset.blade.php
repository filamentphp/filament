@props([
    'label' => null,
])

<fieldset
    {{
        $attributes->class([
            'filament-fieldset-component rounded-xl p-6 ring-1 ring-gray-950/10 dark:ring-white/20',
        ])
    }}
>
    @if (filled($label))
        <legend class="text-sm font-medium leading-6">
            {{ $label }}
        </legend>
    @endif

    {{ $slot }}
</fieldset>
