@props(['field'])

<x-filament-forms::field-wrapper
    :field="$field"
    class="field-wrapper-blade-component"
>
    {{ $slot }}
</x-filament-forms::field-wrapper>
