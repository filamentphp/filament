<x-tables::button
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$getAction()"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    class="filament-pages-actions-modal-actions-button-action"
>
    {{ $getLabel() }}
</x-tables::button>
