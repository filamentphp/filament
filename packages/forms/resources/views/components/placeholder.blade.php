<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes()) }}>
        @if($getAsHtml())
            {!! $getContent() !!}
        @else
            {{ $getContent() }}
        @endif
    </div>
</x-forms::field-wrapper>
