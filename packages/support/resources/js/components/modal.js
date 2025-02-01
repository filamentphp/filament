export default ({ id }) => ({
    isOpen: false,

    livewire: null,

    init: function () {
        this.$watch('isOpen', () => {
            this.isOpen ? this.$refs.dialog.showModal() : this.$refs.dialog.close()
        })
    },

    close: function () {
        this.closeQuietly()

        this.$refs.modalContainer.dispatchEvent(
            new CustomEvent('modal-closed', { id }),
        )
    },

    closeQuietly: function () {
        this.isOpen = false
    },

    open: function () {
        this.$nextTick(() => {
            this.isOpen = true

            this.$dispatch('x-modal-opened')
        })
    },
})
