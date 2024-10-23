@php
    $id = $getId();
    $key = $getKey();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $activeStepClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-active',
        'p-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $inactiveStepClasses = 'invisible absolute h-0 overflow-hidden p-0';
@endphp

<div
    x-bind:tabindex="$el.querySelector('[autofocus]') ? '-1' : '0'"
    x-bind:class="{
        @js($activeStepClasses): step === @js($key),
        @js($inactiveStepClasses): step !== @js($key),
    }"
    x-on:expand="
        if (! isStepAccessible(@js($key))) {
            return
        }

        step = @js($key)
    "
    x-ref="step-{{ $key }}"
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-wizard-step outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
