export default () => ({
    isSticky: false,

    width: 0,

    resizeObserver: null,

    boundUpdateWidth: null,

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

        const actionsComputedStyle = getComputedStyle(
            this.$root.querySelector('.fi-ac'),
        )

        this.width =
            parent.offsetWidth +
            parseInt(actionsComputedStyle.marginInlineStart, 10) * -1 +
            parseInt(actionsComputedStyle.marginInlineEnd, 10) * -1
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
