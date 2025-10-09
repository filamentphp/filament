export default ({ id }) => ({
    isOpen: false,

    isWindowVisible: false,

    livewire: null,

    textSelectionClosePreventionMouseDownHandler: null,

    textSelectionClosePreventionMouseUpHandler: null,

    textSelectionClosePreventionClickHandler: null,

    init() {
        this.$nextTick(() => {
            this.isWindowVisible = this.isOpen

            this.setUpTextSelectionClosePrevention()

            this.$watch('isOpen', () => (this.isWindowVisible = this.isOpen))
        })
    },

    setUpTextSelectionClosePrevention() {
        // Ensure that the click element is not triggered from a user selecting text inside an input.
        // https://github.com/filamentphp/filament/pull/18022

        const windowSelector = '.fi-modal-window'
        const closeOverlaySelector = '.fi-modal-close-overlay'

        const capture = true

        let isMouseDownOnModal = false
        let mouseDownTime = 0

        this.textSelectionClosePreventionClickHandler = (event) => {
            event.stopPropagation()
            event.preventDefault()

            document.removeEventListener(
                'click',
                this.textSelectionClosePreventionClickHandler,
                capture,
            )
        }

        const isCloseOverlayClick = (event) => {
            return (
                !event.target.closest(windowSelector) &&
                (event.target.closest(closeOverlaySelector) ||
                    event.target.closest('body'))
            )
        }

        this.textSelectionClosePreventionMouseDownHandler = (event) => {
            mouseDownTime = Date.now()
            isMouseDownOnModal = !!event.target.closest(windowSelector)
        }

        this.textSelectionClosePreventionMouseUpHandler = (event) => {
            const isClick = Date.now() - mouseDownTime < 75

            if (isMouseDownOnModal && isCloseOverlayClick(event) && !isClick) {
                document.addEventListener(
                    'click',
                    this.textSelectionClosePreventionClickHandler,
                    capture,
                )
            } else {
                document.removeEventListener(
                    'click',
                    this.textSelectionClosePreventionClickHandler,
                    capture,
                )
            }

            isMouseDownOnModal = false
        }

        document.addEventListener(
            'mousedown',
            this.textSelectionClosePreventionMouseDownHandler,
            capture,
        )
        document.addEventListener(
            'mouseup',
            this.textSelectionClosePreventionMouseUpHandler,
            capture,
        )
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

    destroy() {
        const capture = true

        if (this.textSelectionClosePreventionMouseDownHandler) {
            document.removeEventListener(
                'mousedown',
                this.textSelectionClosePreventionMouseDownHandler,
                capture,
            )
            this.textSelectionClosePreventionMouseDownHandler = null
        }

        if (this.textSelectionClosePreventionMouseUpHandler) {
            document.removeEventListener(
                'mouseup',
                this.textSelectionClosePreventionMouseUpHandler,
                capture,
            )
            this.textSelectionClosePreventionMouseUpHandler = null
        }

        if (this.textSelectionClosePreventionClickHandler) {
            document.removeEventListener(
                'click',
                this.textSelectionClosePreventionClickHandler,
                capture,
            )
            this.textSelectionClosePreventionClickHandler = null
        }
    },
})
