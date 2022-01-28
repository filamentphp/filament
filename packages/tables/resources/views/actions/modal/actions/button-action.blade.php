<x-tables::button
    :form="$getForm()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$getAction()"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    class="filament-tables-actions-modal-button-action"

>
    {{ $getLabel() }}
</x-tables::button>
