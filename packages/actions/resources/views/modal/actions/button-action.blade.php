@php
    if ($getUrl()) {
        $wireClickAction = null;
    } elseif ($getAction()) {
        $wireClickAction = $getAction();

        if ($getActionArguments()) {
            $wireClickAction .= '(\'';
            $wireClickAction .= str(json_encode($getActionArguments()))->replace('"', '\\"');
            $wireClickAction .= '\')';
        }
    } else {
        $wireClickAction = null;
    }
@endphp

<x-filament::button
    :form="$getFormToSubmit()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :tag="$getUrl() ? 'a' : 'button'"
    :wire:click="$wireClickAction"
    :href="$isEnabled() ? $getUrl() : null"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :x-on:click="$canCancelAction() ? 'close()' : null"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    :size="$getSize()"
    :attributes="$getExtraAttributeBag()"
    class="filament-actions-modal-button-action"
>
    {{ $getLabel() }}
</x-filament::button>
