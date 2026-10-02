export default function textareaFormComponent({
    initialHeight,
    shouldAutosize,
    state,
}) {
    return {
        state,

        resizeObserver: null,

        wrapperEl: null,

        init() {
            this.wrapperEl = this.$el.parentNode

            this.setInitialHeight()

            if (shouldAutosize) {
                this.$watch('state', () => {
                    this.resize()
                })
            } else {
                this.setUpResizeObserver()
            }
        },

        setInitialHeight() {
            if (this.$el.scrollHeight <= 0) {
                return
            }

            this.wrapperEl.style.height = initialHeight + 'rem'
        },

        resize() {
            if (this.$el.scrollHeight <= 0) {
                return
            }

            const previousHeight = this.$el.style.height
            this.$el.style.height = '0px'

            const contentHeight = this.$el.scrollHeight
            this.$el.style.height = previousHeight

            const minHeightPx =
                parseFloat(initialHeight) *
                parseFloat(getComputedStyle(document.documentElement).fontSize)
            const newHeight = Math.max(contentHeight, minHeightPx) + 'px'

            if (this.wrapperEl.style.height === newHeight) {
                return
            }

            this.wrapperEl.style.height = newHeight
        },

        setUpResizeObserver() {
            this.resizeObserver = new ResizeObserver(() => {
                this.wrapperEl.style.height = this.$el.style.height
            })

            this.resizeObserver.observe(this.$el)
        },

        destroy() {
            this.resizeObserver?.disconnect()
        },
    }
}
