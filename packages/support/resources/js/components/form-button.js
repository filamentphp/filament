export default () => ({
    form: null,

    isProcessing: false,

    processingMessage: null,

    init() {
        const formElement = this.$el.closest('form')

        formElement?.addEventListener('form-processing-started', (event) => {
            this.isProcessing = true
            this.processingMessage = event.detail.message
        })

        formElement?.addEventListener('form-processing-finished', () => {
            this.isProcessing = false
        })
    },
})
