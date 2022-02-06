<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'p-6 bg-white shadow rounded-xl filament-forms-card-component',
        'dark:bg-gray-800' => config('forms.dark_mode'),
    ]) }}
>
    {{ $getChildComponentContainer() }}
</div>
