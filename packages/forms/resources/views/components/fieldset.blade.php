<fieldset
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'rounded-xl shadow-sm border border-gray-300 p-6 filament-forms-fieldset-component',
        'dark:border-gray-600 dark:text-gray-200' => config('forms.dark_mode'),
    ]) }}
>
    <legend class="text-sm leading-tight font-medium px-2 -ml-2">
        {{ $getLabel() }}
    </legend>

    {{ $getChildComponentContainer() }}
</fieldset>
