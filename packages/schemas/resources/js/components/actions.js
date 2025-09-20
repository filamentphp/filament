export default () => ({
    isSticky: false,

    enableSticky() {
        this.isSticky = this.$el.getBoundingClientRect().top > 0
    },

    disableSticky() {
        this.isSticky = false
    },
})
