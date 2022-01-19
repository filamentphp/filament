<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ $getId() }}'"
    {{ $attributes->merge($getExtraAttributes())->class(['p-6 focus:outline-none', 'filament-forms-components-tabs-tab']) }}
>
    {{ $getChildComponentContainer() }}
</div>
