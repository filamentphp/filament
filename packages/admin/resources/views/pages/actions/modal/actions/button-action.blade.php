@php
    if (! $getAction()) {
        $wireClickAction = null;
    } else {
        $wireClickAction = $getAction();

        if ($getActionArguments()) {
            $wireClickAction .= '(\'';
            $wireClickAction .= \Illuminate\Support\Str::of(json_encode($getActionArguments()))->replace('"', '\\"');
            $wireClickAction .= '\')';
        }
    }
@endphp

<x-filament::button
    :form="$getForm()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$wireClickAction"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    :size="$getSize()"
    :attributes="$getExtraAttributeBag()"
    class="filament-page-modal-button-action"
>
    {{ $getLabel() }}
</x-filament::button>
