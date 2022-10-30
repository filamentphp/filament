<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class(['filament-forms-card-component p-6 bg-white rounded-xl ring-1 ring-gray-900/10 dark:ring-gray-50/10 dark:bg-gray-800'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
