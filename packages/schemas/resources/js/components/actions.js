export default () => ({
    isSticky: false,
    parentWidth: 0,

    enableSticky() {
        this.isSticky = this.$el.getBoundingClientRect().top > 0
    },

    disableSticky() {
        this.isSticky = false
    },

    updateWidth() {
        const parent = this.$el.parentElement
        if (!parent) {
            return
        }

        this.parentWidth = parent.offsetWidth
    },

    init() {
        const parent = this.$el.parentElement
        if (!parent) {
            return
        }

        this.updateWidth()

        this.resizeObserver = new ResizeObserver(() => this.updateWidth())
        this.resizeObserver.observe(parent)

        this.boundUpdateWidth = this.updateWidth.bind(this)
        window.addEventListener('resize', this.boundUpdateWidth)
    },

    destroy() {
        if (this.resizeObserver) {
            this.resizeObserver.disconnect()
            this.resizeObserver = null
        }

        if (this.boundUpdateWidth) {
            window.removeEventListener('resize', this.boundUpdateWidth)
            this.boundUpdateWidth = null
        }
    },
})
