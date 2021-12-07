<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <ul class="space-y-2">
        @foreach ($getChildComponentContainers() as $uuid => $item)
            <li wire:key="{{ $item->getStatePath() }}">
                {{ $item }}
            </li>
        @endforeach
    </ul>
</x-forms::field-wrapper>
