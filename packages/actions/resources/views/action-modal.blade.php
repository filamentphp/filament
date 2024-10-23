<form wire:submit.prevent="callMountedAction">
    <x-filament::modal
        :alignment="$action->getModalAlignment()"
        :autofocus="$action?->isModalAutofocused()"
        :close-button="$action?->hasModalCloseButton()"
        :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
        :close-by-escaping="$action?->isModalClosedByEscaping()"
        :description="$action->getModalDescription()"
        :extra-modal-window-attribute-bag="$action->getExtraModalWindowAttributeBag()"
        :footer-actions="$action->getVisibleModalFooterActions()"
        :footer-actions-alignment="$action->getModalFooterActionsAlignment()"
        :heading="$action->getModalHeading()"
        :icon="$action->getModalIcon()"
        :icon-color="$action->getModalIconColor()"
        :id="'fi-' . $this->getId() . '-action-' . $action->getNestingIndex()"
        :slide-over="$action->isModalSlideOver()"
        :sticky-footer="$action->isModalFooterSticky()"
        :sticky-header="$action->isModalHeaderSticky()"
        :width="$action->getModalWidth()"
        :wire:key="$this->getId() . '.actions.' . $action->getName() . '.modal'"
        x-on:modal-closed.stop="$wire.unmountAction(false)"
    >
        {{ $action->getModalContent() }}

        @if ($this->mountedActionHasSchema(mountedAction: $action))
            {{ $this->getMountedActionSchema(mountedAction: $action) }}
        @endif

        {{ $action->getModalContentFooter() }}
    </x-filament::modal>
</form>
