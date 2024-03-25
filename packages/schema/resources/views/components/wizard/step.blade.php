@php
    $id = $getId();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $activeStepClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-active',
        'p-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $inactiveStepClasses = 'invisible h-0 overflow-y-hidden p-0';
@endphp

<div
    x-bind:class="{
        @js($activeStepClasses): step === @js($id),
        @js($inactiveStepClasses): step !== @js($id),
    }"
    x-on:expand="
        if (! isStepAccessible(@js($id))) {
            return
        }

        step = @js($id)
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
