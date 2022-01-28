<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['p-6 bg-white shadow rounded-xl dark:bg-dark-800', 'filament-forms-card-component']) }}
>
    {{ $getChildComponentContainer() }}
</div>
