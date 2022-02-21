<x-filament::button
    :form="$getForm()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$getAction()"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    class="filament-page-modal-button-action"
>
    {{ $getLabel() }}
</x-filament::button>
