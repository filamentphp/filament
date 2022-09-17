<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300',
        'dark:border-gray-600 dark:bg-gray-800' => config('forms.dark_mode'),
    ]) }}
>
    {{ $getChildComponentContainer() }}
</div>
