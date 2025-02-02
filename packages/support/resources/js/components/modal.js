export default ({ id }) => ({
    isOpen: false,

    isModalWindowVisible: false,

    livewire: null,

    init: function () {
        this.$watch('isOpen', () => {
            if (this.isOpen) {
                this.$root?.showModal()

                this.isModalWindowVisible = true

                return;
            }

            this.isModalWindowVisible = false

            setTimeout(
                () => this.$root?.close(),
                this.$refs.window ? ((parseFloat(window.getComputedStyle(this.$refs.window).transitionDuration)) * 1000) : 0,
            )
        })
    },

    close: function () {
        this.closeQuietly()

        setTimeout(
            () => this.$refs.windowContainer?.dispatchEvent(
                new CustomEvent('modal-closed', { id }),
            ),
            this.$refs.window ? ((parseFloat(window.getComputedStyle(this.$refs.window).transitionDuration)) * 1000) : 0,
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
