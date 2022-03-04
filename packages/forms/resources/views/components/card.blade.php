<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'p-6 bg-white rounded-xl border border-gray-300 filament-forms-card-component',
        'dark:border-gray-600 dark:bg-gray-800' => config('forms.dark_mode'),
    ]) }}
>
    {{ $getChildComponentContainer() }}
</div>
