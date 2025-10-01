export default ({ id }) => ({
    isOpen: false,

    isWindowVisible: false,

    livewire: null,

    init() {
        this.$nextTick(() => {
            this.isWindowVisible = this.isOpen

            setupModalClickGuard({
                modalSelector: '.fi-modal-window',
                backdropSelector: '.fi-modal-close-overlay',
                pageSelector: '.fi-page',
            })

            this.$watch('isOpen', () => (this.isWindowVisible = this.isOpen))
        })

        function setupModalClickGuard({ modalSelector, backdropSelector, pageSelector, clickThreshold = 75 }) {
            let mouseDownOnModal = false
            let mouseDownTime = 0

            const preventClick = (event) => {
                event.stopPropagation()
                event.preventDefault()
                document.removeEventListener('click', preventClick, true)
            }

            const isBackdropClick = (e) => {
                return !e.target.closest(modalSelector) &&
                    (e.target.closest(backdropSelector) || e.target.closest(pageSelector))
            }

            document.addEventListener('mousedown', (e) => {
                mouseDownTime = Date.now()
                mouseDownOnModal = !!e.target.closest(modalSelector)
            }, true)

            document.addEventListener('mouseup', (e) => {
                const isClick = Date.now() - mouseDownTime < clickThreshold

                if (mouseDownOnModal && isBackdropClick(e) && !isClick) {
                    document.addEventListener('click', preventClick, true)
                } else {
                    document.removeEventListener('click', preventClick, true)
                }

                mouseDownOnModal = false
            }, true)
        }
    },

    close() {
        this.closeQuietly()

        this.$dispatch('modal-closed', { id })
    },

    closeQuietly() {
        this.isOpen = false
    },

    open() {
        this.$nextTick(() => {
            this.isOpen = true

            document.dispatchEvent(
                new CustomEvent('x-modal-opened', {
                    bubbles: true,
                    composed: true,
                    detail: { id },
                }),
            )
        })
    },
})
