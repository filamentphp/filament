<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{
        $attributes->merge($getExtraAttributes())->class([
            'filament-forms-card-component rounded-xl border border-gray-300 bg-white p-6',
            'dark:border-gray-600 dark:bg-gray-800' => config('forms.dark_mode'),
        ])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
