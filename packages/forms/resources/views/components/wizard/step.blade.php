<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    x-bind:class="{ 'invisible h-0 overflow-y-hidden': step !== '{{ $getId() }}' }"
    {{ $attributes->merge($getExtraAttributes())->class(['focus:outline-none filament-forms-wizard-component-step']) }}
>
    {{ $getChildComponentContainer() }}
</div>
