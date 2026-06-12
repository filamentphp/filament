export default ({ livewireId }) => ({
    actionNestingIndex: null,

    closedActionNestingIndexes: [],

    init() {
        window.addEventListener('sync-action-modals', (event) => {
            if (event.detail.id !== livewireId) {
                return
            }

            this.syncActionModals(
                event.detail.newActionNestingIndex,
                event.detail.shouldOverlayParentActions ?? false,
            )
        })

        window.addEventListener('modal-closed', (event) => {
            const actionNestingIndex = this.getActionNestingIndexFromModalId(
                event.detail.id,
            )

            if (actionNestingIndex === null) {
                return
            }

            this.closedActionNestingIndexes.push(actionNestingIndex)
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
            this.closedActionNestingIndexes = []

            return
        }

        this.closedActionNestingIndexes =
            this.closedActionNestingIndexes.filter(
                (closedActionNestingIndex) =>
                    closedActionNestingIndex <= this.actionNestingIndex,
            )

        if (this.closedActionNestingIndexes.includes(this.actionNestingIndex)) {
            return
        }

        if (
            !this.$el.querySelector(
                `#${this.generateModalId(newActionNestingIndex)}`,
            )
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

    getActionNestingIndexFromModalId(id) {
        const prefix = `fi-${livewireId}-action-`

        if (!id?.startsWith(prefix)) {
            return null
        }

        const actionNestingIndex = Number(id.slice(prefix.length))

        return Number.isInteger(actionNestingIndex) ? actionNestingIndex : null
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
