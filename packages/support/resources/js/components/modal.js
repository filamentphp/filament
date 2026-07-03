let scrollLockCount = 0

let restoreScroll = null

const acquireScrollLock = () => {
    if (scrollLockCount === 0) {
        const overflow = document.documentElement.style.overflow
        const paddingRight = document.documentElement.style.paddingRight

        const scrollbarWidth =
            window.innerWidth - document.documentElement.clientWidth
        const scrollbarGutter = window.getComputedStyle(
            document.documentElement,
        ).scrollbarGutter

        document.documentElement.style.overflow = 'hidden'

        // A reserved scrollbar gutter already compensates for the scrollbar.
        if (scrollbarGutter && scrollbarGutter !== 'auto') {
            restoreScroll = () => {
                document.documentElement.style.overflow = overflow
            }
        } else {
            document.documentElement.style.paddingRight = `${scrollbarWidth}px`

            restoreScroll = () => {
                document.documentElement.style.overflow = overflow
                document.documentElement.style.paddingRight = paddingRight
            }
        }
    }

    scrollLockCount++
}

const releaseScrollLock = () => {
    if (scrollLockCount === 0) {
        return
    }

    scrollLockCount--

    if (scrollLockCount === 0) {
        restoreScroll?.()
        restoreScroll = null
    }
}

export default ({ id, isScrollLocked = true }) => ({
    isOpen: false,

    isWindowVisible: false,

    isTrapActive: false,

    isHoldingScrollLock: false,

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

    isTopmost() {
        if (!id) {
            return true
        }

        const openModals = document.querySelectorAll('.fi-modal-open')

        if (openModals.length === 0) {
            return false
        }

        return openModals[openModals.length - 1].id === id
    },

    acquireScrollLock() {
        if (this.isHoldingScrollLock) {
            return
        }

        this.isHoldingScrollLock = true

        acquireScrollLock()
    },

    releaseScrollLock() {
        if (!this.isHoldingScrollLock) {
            return
        }

        this.isHoldingScrollLock = false

        releaseScrollLock()
    },

    close() {
        this.closeQuietly()

        this.isTrapActive = false

        this.releaseScrollLock()

        this.$dispatch('modal-closed', { id })
    },

    closeQuietly() {
        this.isOpen = false
    },

    open() {
        this.$nextTick(() => {
            this.isOpen = true
            this.isTrapActive = true

            // Click-through modals let you interact with the page behind them,
            // so they must not lock scrolling.
            if (isScrollLocked) {
                this.acquireScrollLock()
            }

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
        // Release in case the modal is removed while still holding the lock.
        this.releaseScrollLock()

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
