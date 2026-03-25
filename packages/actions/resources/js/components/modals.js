export default ({ livewireId }) => ({
    actionNestingIndex: null,

    dismissedModals: new Set(),

    init() {
        this.$el.addEventListener('modal-closed', (event) => {
            if (event.detail?.id) {
                this.dismissedModals.add(event.detail.id)
            }
        })

        window.addEventListener('sync-action-modals', (event) => {
            if (event.detail.id !== livewireId) {
                return
            }

            this.syncActionModals(
                event.detail.newActionNestingIndex,
                event.detail.shouldOverlayParentActions ?? false,
            )
        })
    },

    syncActionModals(
        newActionNestingIndex,
        shouldOverlayParentActions = false,
    ) {
        if (this.actionNestingIndex === newActionNestingIndex) {
            // https://github.com/filamentphp/filament/issues/16474
            this.actionNestingIndex !== null &&
                this.$nextTick(() => this.openModal())

            return
        }

        const isNestingIncrease =
            this.actionNestingIndex !== null &&
            newActionNestingIndex !== null &&
            newActionNestingIndex > this.actionNestingIndex

        if (
            this.actionNestingIndex !== null &&
            !(shouldOverlayParentActions && isNestingIncrease)
        ) {
            this.closeModal()
        }

        this.actionNestingIndex = newActionNestingIndex

        if (this.actionNestingIndex === null) {
            this.dismissedModals.clear()

            return
        }

        const modalId = this.generateModalId(newActionNestingIndex)

        // If the user already closed this modal locally (e.g. by pressing
        // Escape twice quickly), do not reopen it. The pending Livewire
        // request will handle the server-side unmount.
        if (this.dismissedModals.has(modalId)) {
            this.dismissedModals.delete(modalId)

            return
        }

        if (
            !this.$el.querySelector(`#${modalId}`)
        ) {
            this.$nextTick(() => this.openModal())

            return
        }

        this.openModal()
    },

    generateModalId(actionNestingIndex) {
        // HTML IDs must start with a letter, so if the Livewire component ID starts
        // with a number, we need to make sure it does not fail by prepending `fi-`.
        return `fi-${livewireId}-action-` + actionNestingIndex
    },

    openModal() {
        const id = this.generateModalId(this.actionNestingIndex)

        document.dispatchEvent(
            new CustomEvent('open-modal', {
                bubbles: true,
                composed: true,
                detail: { id },
            }),
        )
    },

    closeModal() {
        const id = this.generateModalId(this.actionNestingIndex)

        document.dispatchEvent(
            new CustomEvent('close-modal-quietly', {
                bubbles: true,
                composed: true,
                detail: { id },
            }),
        )
    },
})
