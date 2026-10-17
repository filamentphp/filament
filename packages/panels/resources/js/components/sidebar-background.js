export default () => ({
    hasOpenModal: false,

    isDestroyed: false,

    modalObserver: null,

    init() {
        this.$watch('$store.sidebar.isOpenMobile', () =>
            this.setUpModalObserver(),
        )

        this.setUpModalObserver()
    },

    setUpModalObserver() {
        if (this.isDestroyed) {
            return
        }

        this.modalObserver?.disconnect()
        this.modalObserver = null
        this.hasOpenModal = false

        if (!this.$store.sidebar.isOpenMobile) {
            return
        }

        const updateModalState = () => {
            this.hasOpenModal = !!this.$el.querySelector(
                '.fi-modal-open[aria-modal="true"]',
            )
        }

        updateModalState()

        this.modalObserver = new MutationObserver(updateModalState)
        this.modalObserver.observe(this.$el, {
            attributes: true,
            attributeFilter: ['class', 'aria-modal'],
            childList: true,
            subtree: true,
        })
    },

    destroy() {
        this.isDestroyed = true
        this.modalObserver?.disconnect()
        this.modalObserver = null
    },
})
