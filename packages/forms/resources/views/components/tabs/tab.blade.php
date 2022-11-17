<div
    x-bind:class="{ 'invisible h-0 p-0 overflow-y-hidden': tab !== '{{ $getId() }}', 'p-6': tab === '{{ $getId() }}' }"
    x-on:expand-concealing-component.window="
        error = $el.querySelector('[data-validation-error]')

        if (! error) {
            return
        }

        tab = @js($getId())

        if (document.body.querySelector('[data-validation-error]') !== error) {
            return
        }

        setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 200)
    "
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $getId(),
                'id' => $getId(),
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: true)
            ->merge($getExtraAttributes(), escape: true)
            ->class(['filament-forms-tabs-component-tab focus:outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
