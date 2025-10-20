export default () => ({
    toggle(event) {
        this.$refs.panel?.toggle(event)
    },

    open(event) {
        this.$refs.panel?.open(event)
    },

    close(event) {
        this.$refs.panel?.close(event)
    },
})
