export default ({ livewireId }) => ({
    actionNestingIndex: null,

    init: function () {
        window.addEventListener('sync-action-modals', (event) => {
            if (event.detail.id !== livewireId) {
                return
            }

            this.syncActionModals(event.detail.newActionNestingIndex)
        })
    },

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

    generateModalId: function (actionNestingIndex) {
        // HTML IDs must start with a letter, so if the Livewire component ID starts
        // with a number, we need to make sure it does not fail by prepending `fi-`.
        return `fi-${livewireId}-action-` + actionNestingIndex
    },

    openModal: function () {
        const id = this.generateModalId(this.actionNestingIndex)

        this.$dispatch('open-modal', { id })
    },

    closeModal: function () {
        const id = this.generateModalId(this.actionNestingIndex)

        this.$dispatch('close-modal-quietly', { id })
    },
})
