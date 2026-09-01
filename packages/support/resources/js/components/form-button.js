export default () => ({
    form: null,

    processingCount: 0,

    isProcessing: false,

    processingMessage: null,

    init() {
        const formElement = this.$el.closest('form')

        formElement?.addEventListener('form-processing-started', (event) => {
            this.processingCount++
            this.isProcessing = true
            this.processingMessage = event.detail.message
        })

        formElement?.addEventListener('form-processing-finished', () => {
            this.processingCount = Math.max(0, this.processingCount - 1)
            this.isProcessing = this.processingCount > 0
        })
    },
})
