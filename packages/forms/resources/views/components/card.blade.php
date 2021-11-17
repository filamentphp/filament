<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['p-6 bg-white shadow rounded-xl']) }}
>
    {{ $getChildComponentContainer() }}
</div>
