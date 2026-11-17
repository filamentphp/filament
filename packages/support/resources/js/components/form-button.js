export default () => ({
    form: null,

    eventListenersController: null,

    loadingObserver: null,

    isLoading: false,

    processingCount: 0,

    isProcessing: false,

    processingMessage: null,

    init() {
        this.form = this.$el.form ?? this.$el.closest('form')

        this.eventListenersController = new AbortController()

        const { signal } = this.eventListenersController

        this.isLoading = this.$el.hasAttribute('data-loading')

        this.loadingObserver = new MutationObserver(() => {
            this.isLoading = this.$el.hasAttribute('data-loading')
        })

        this.loadingObserver.observe(this.$el, {
            attributes: true,
            attributeFilter: ['data-loading'],
        })

        this.form?.addEventListener(
            'submit',
            (event) => {
                if (!this.isProcessing) {
                    return
                }

                event.preventDefault()
                event.stopImmediatePropagation()
            },
            { capture: true, signal },
        )

        this.form?.addEventListener(
            'form-processing-started',
            (event) => {
                this.processingCount++
                this.isProcessing = true
                this.$el.classList.add('fi-processing')
                this.processingMessage = event.detail.message
            },
            { signal },
        )

        this.form?.addEventListener(
            'form-processing-finished',
            () => {
                this.processingCount = Math.max(0, this.processingCount - 1)
                this.isProcessing = this.processingCount > 0
                this.$el.classList.toggle('fi-processing', this.isProcessing)
            },
            { signal },
        )
    },

    destroy() {
        this.eventListenersController?.abort()
        this.loadingObserver?.disconnect()
        this.$el.classList.remove('fi-processing')
    },
})
