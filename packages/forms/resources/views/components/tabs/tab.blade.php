<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    x-bind:class="{ 'invisible h-0 p-0 overflow-y-hidden': tab !== '{{ $getId() }}', 'p-6': tab === '{{ $getId() }}' }"
    {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-tabs-component-tab focus:outline-none']) }}
>
    {{ $getChildComponentContainer() }}
</div>
