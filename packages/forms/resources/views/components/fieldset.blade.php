<fieldset
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'filament-forms-fieldset-component rounded-xl border border-gray-300 p-6 shadow-sm',
                'dark:border-gray-600 dark:text-gray-200' => config('forms.dark_mode'),
            ])
    }}
>
    @if (filled($label = $getLabel()))
        <legend class="-ml-2 px-2 text-sm font-medium leading-tight">
            {{ $label }}
        </legend>
    @endif

    {{ $getChildComponentContainer() }}
</fieldset>
