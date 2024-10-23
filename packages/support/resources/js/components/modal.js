export default ({ id }) => ({
    isOpen: false,

    livewire: null,

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

            this.$dispatch('ax-modal-opened')
        })
    },
})
