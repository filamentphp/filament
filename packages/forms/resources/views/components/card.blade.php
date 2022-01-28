<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['p-6 bg-white shadow rounded-xl', 'filament-forms-card-component']) }}
>
    {{ $getChildComponentContainer() }}
</div>
