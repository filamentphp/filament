@props(['field'])

<x-filament-forms::field-wrapper
    :field="$field"
    :label-prefix="$attributes->get('label-prefix')"
    :label-suffix="$attributes->get('label-suffix')"
    :label-tag="$attributes->get('label-tag', 'label')"
    :inline-label-vertical-alignment="$attributes->get('inline-label-vertical-alignment')"
    :has-errors="$attributes->get('has-errors', true)"
    :error-message="$attributes->get('error-message')"
    :error-messages="$attributes->get('error-messages')"
    :are-html-error-messages-allowed="$attributes->get('are-html-error-messages-allowed')"
    :should-show-all-validation-messages="$attributes->get('should-show-all-validation-messages')"
    {{ $attributes->class(['field-wrapper-blade-component']) }}
>
    {{ $slot }}
</x-filament-forms::field-wrapper>
