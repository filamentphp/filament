export default () => ({
    form: null,

    eventListenersController: null,

    processingCount: 0,

    isProcessing: false,

    processingMessage: null,

    init() {
        this.form = this.$el.closest('form')

        this.eventListenersController = new AbortController()

        const { signal } = this.eventListenersController

        this.form?.addEventListener(
            'form-processing-started',
            (event) => {
                this.processingCount++
                this.isProcessing = true
                this.processingMessage = event.detail.message
            },
            { signal },
        )

        this.form?.addEventListener(
            'form-processing-finished',
            () => {
                this.processingCount = Math.max(0, this.processingCount - 1)
                this.isProcessing = this.processingCount > 0
            },
            { signal },
        )
    },

    destroy() {
        this.eventListenersController?.abort()
    },
})
