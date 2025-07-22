<x-dynamic-component
    :component="$getFieldWrapperView()"
    :has-inline-label="$hasInlineLabel()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-actions="$getHintActions()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :hint-icon-tooltip="$getHintIconTooltip()"
    :state-path="$getStatePath()"
>
    @php
        $copyableState = $getContent();
        $copyMessage = $getCopyMessage($state);
        $copyMessageDuration = $getCopyMessageDuration($state);
        $itemIsCopyable = $isCopyable($state);
    @endphp
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-placeholder text-sm leading-6',
                    'cursor-pointer' => $itemIsCopyable,
                ])
        }}

        @if ($itemIsCopyable)
            x-on:click="
                window.navigator.clipboard.writeText(@js($copyableState))
                $tooltip(@js($copyMessage), {
                    theme: $store.theme,
                    timeout: @js($copyMessageDuration),
                })
            "
        @endif
    >
        {{ $getContent() }}
    </div>
</x-dynamic-component>
