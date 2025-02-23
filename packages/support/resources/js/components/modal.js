export default ({ id }) => ({
    isOpen: false,

    isWindowVisible: false,

    livewire: null,

    init: function () {
        this.$nextTick(() => {
            this.isWindowVisible = this.isOpen

            this.$watch('isOpen', () => (this.isWindowVisible = this.isOpen))
        })
    },

    close: function () {
        this.closeQuietly()

        this.$dispatch('modal-closed', { id })
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
