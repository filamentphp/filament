<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    data-tabs-panel
    x-bind:class="{ 'invisible h-0 p-0 overflow-y-hidden': tab !== '{{ $getId() }}', 'p-6': tab === '{{ $getId() }}' }"
    x-on:expand-concealing-component.window="
        if (
            $el.querySelector('[data-validation-error]') &&
            ($el.closest('div[data-tabs-container]').querySelector('[data-tabs-panel]') === $el)
        ) {
            tab = @js($getId())
        }
    "
    {{ $attributes->merge($getExtraAttributes())->class(['focus:outline-none filament-forms-tabs-component-tab']) }}
>
    {{ $getChildComponentContainer() }}
</div>
