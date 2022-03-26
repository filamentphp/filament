<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    x-bind:class="{ 'invisible h-0 p-0 overflow-y-hidden': tab !== '{{ $getId() }}' }"
    {{ $attributes->merge($getExtraAttributes())->class(['p-6 focus:outline-none filament-forms-tabs-component-tab']) }}
>
    {{ $getChildComponentContainer() }}
</div>
