@php
    $id = $getId();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $visibleStepClasses = \Illuminate\Support\Arr::toCssClasses([
        'p-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $invisibleStepClasses = 'invisible h-0 overflow-y-hidden p-0';
@endphp

<div
    x-bind:class="step === @js($id) ? @js($visibleStepClasses) : @js($invisibleStepClasses)"
    x-on:expand-concealing-component.window="
        $nextTick(() => {
            error = $el.querySelector('[data-validation-error]')

            if (! error) {
                return
            }

            if (! isStepAccessible(step, @js($id))) {
                return
            }

            step = @js($id)

            if (document.body.querySelector('[data-validation-error]') !== error) {
                return
            }

            setTimeout(
                () =>
                    $el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'start',
                    }),
                200,
            )
        })
    "
    x-ref="step-{{ $id }}"
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-wizard-step outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
