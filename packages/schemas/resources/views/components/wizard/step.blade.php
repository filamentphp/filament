@php
    $id = $getId();
    $key = $getKey();
    $wizard = $getContainer()->getParentComponent();
    $isContained = $wizard->isContained();
    $alpineSubmitHandler = $hasFormWrapper() ? $wizard->getAlpineSubmitHandler() : null;
@endphp

<{{ filled($alpineSubmitHandler) ? 'form' : 'div' }}
    x-bind:tabindex="$el.querySelector('[autofocus]') ? '-1' : '0'"
    x-bind:class="{
        'fi-active': step === @js($key),
    }"
    x-on:expand="
        if (! isStepAccessible(@js($key))) {
            return
        }

        step = @js($key)
    "
    @if (filled($alpineSubmitHandler))
        x-on:submit.prevent="isLastStep() ? {!! $alpineSubmitHandler !!} : requestNextStep()"
    @endif
    x-cloak
    x-ref="step-{{ $key }}"
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-sc-wizard-step'])
    }}
>
    {{ $getChildSchema() }}

    @if (filled($alpineSubmitHandler))
        {{-- This is a hack to allow the form to submit when the user presses the enter key, even if there is no other submit button in the form. --}}
        <input type="submit" hidden />
    @endif
</{{ filled($alpineSubmitHandler) ? 'form' : 'div' }}>
