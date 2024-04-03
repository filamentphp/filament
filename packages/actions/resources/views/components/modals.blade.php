@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <div
        x-data="{
            actionNestingIndex: null,

            syncActionModals: function (newActionNestingIndex) {
                if (this.actionNestingIndex === newActionNestingIndex) {
                    return
                }

                if (this.actionNestingIndex !== null) {
                    this.closeModal()
                }

                this.actionNestingIndex = newActionNestingIndex

                if (this.actionNestingIndex === null) {
                    return
                }

                if (! this.$el.querySelector(`#${this.generateModalId(newActionNestingIndex)}`)) {
                    $nextTick(() => this.openModal())

                    return
                }

                this.openModal()
            },

            generateModalId: function (actionNestingIndex) {
                // HTML IDs must start with a letter, so if the Livewire component ID starts
                // with a number, we need to make sure it does not fail by prepending `fi-`.
                return 'fi-{{ $this->getId() }}-action-' + actionNestingIndex
            },

            openModal: function () {
                const id = this.generateModalId(this.actionNestingIndex)

                if (! this.$el.querySelector(`#${id}`)) {
                    return
                }

                this.$dispatch('open-modal', { id })
            },

            closeModal: function () {
                const id = this.generateModalId(this.actionNestingIndex)

                if (! this.$el.querySelector(`#${id}`)) {
                    return
                }

                this.$dispatch('close-modal-quietly', { id })
            },
        }"
        x-on:sync-action-modals.window="
            if ($event.detail.id === '{{ $this->getId() }}')
                syncActionModals($event.detail.newActionNestingIndex)
        "
    >
        @foreach ($this->getMountedActions() as $actionNestingIndex => $action)
            <form wire:submit.prevent="callMountedAction">
                <x-filament::modal
                    :alignment="$action?->getModalAlignment()"
                    :close-button="$action?->hasModalCloseButton()"
                    :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
                    :description="$action?->getModalDescription()"
                    display-classes="block"
                    :footer-actions="$action?->getVisibleModalFooterActions()"
                    :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
                    :heading="$action?->getModalHeading()"
                    :icon="$action?->getModalIcon()"
                    :icon-color="$action?->getModalIconColor()"
                    :id="'fi-' . $this->getId() . '-action-' . $actionNestingIndex"
                    :slide-over="$action?->isModalSlideOver()"
                    :sticky-footer="$action?->isModalFooterSticky()"
                    :sticky-header="$action?->isModalHeaderSticky()"
                    :width="$action?->getModalWidth()"
                    :wire:key="$action ? $this->getId() . '.actions.' . $action->getName() . '.modal' : null"
                    x-on:modal-closed.stop="$wire.unmountAction(false)"
                >
                    {{ $action->getModalContent() }}

                    @if ($this->mountedActionHasSchema(mountedAction: $action))
                        {{ $this->getMountedActionSchema(mountedAction: $action) }}
                    @endif

                    {{ $action->getModalContentFooter() }}
                </x-filament::modal>
            </form>
        @endforeach
    </div>

    @php
        $this->hasActionsModalRendered = true;
    @endphp
@endif
