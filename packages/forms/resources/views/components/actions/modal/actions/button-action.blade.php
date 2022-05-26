<x-forms::button
    :form="$getForm()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$getAction()"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    :size="$getSize()"
    :attributes="$getExtraAttributeBag()"
    class="filament-forms-modal-button-action"
>
    {{ $getLabel() }}
</x-forms::button>
