export default function textareaFormComponent({
    initialHeight,
    shouldAutosize,
    state,
}) {
    return {
        state,

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

            const newHeight = this.$el.scrollHeight + 'px'
            this.$el.style.height = previousHeight

            if (this.wrapperEl.style.height === newHeight) {
                return
            }

            this.wrapperEl.style.height = newHeight
        },

        setUpResizeObserver() {
            const observer = new ResizeObserver(() => {
                this.wrapperEl.style.height = this.$el.style.height
            })

            observer.observe(this.$el)
        },
    }
}
