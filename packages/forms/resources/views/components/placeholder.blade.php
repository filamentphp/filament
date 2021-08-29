<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :state-path="$getStatePath()"
>
    {{ $getState() }}
</x-forms::field-wrapper>
