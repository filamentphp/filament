<fieldset
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['filament-infolists-fieldset-component rounded-xl border border-gray-300 p-6 shadow-sm dark:border-gray-600 dark:text-gray-200'])
    }}
>
    <legend class="-ms-2 px-2 text-sm font-medium leading-tight">
        {{ $getLabel() }}
    </legend>

    {{ $getChildComponentContainer() }}
</fieldset>
