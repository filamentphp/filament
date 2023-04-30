<fieldset
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6',
        'dark:border-gray-600 dark:text-gray-200' => config('forms.dark_mode'),
    ]) }}
>
    @if (filled($label = $getLabel()))
        <legend class="text-sm leading-tight font-medium px-2 -ml-2">
            {{ $label }}
        </legend>
    @endif

    {{ $getChildComponentContainer() }}
</fieldset>
