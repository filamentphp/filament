export default () => ({
    isSticky: false,

    init() {
        this.evaluatePageScrollPosition()
    },

    evaluatePageScrollPosition() {
        const rect = this.$el.getBoundingClientRect()

        const isBelowViewport = rect.top > window.innerHeight
        const isPartiallyVisible =
            rect.top < window.innerHeight && rect.bottom > window.innerHeight

        this.isSticky = isBelowViewport || isPartiallyVisible
    },
})
