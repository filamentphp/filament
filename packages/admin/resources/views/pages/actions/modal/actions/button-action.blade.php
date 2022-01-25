<x-filament::button
    :form="$getForm()"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :wire:click="$getAction()"
    :x-on:click="$canCancelAction() ? 'isOpen = false' : null"
    :color="$getColor()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
>
    {{ $getLabel() }}
</x-filament::button>
