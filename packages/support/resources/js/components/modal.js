// Keep scroll positions across modal remounts caused by Livewire morphing.
const modalScrollPositions = new Map()

export default ({ id }) => ({
    isOpen: false,

    isWindowVisible: false,

    livewire: null,

    scrollContainer: null,

    scrollListener: null,

    isRestoringScrollPosition: false,

    textSelectionClosePreventionMouseDownHandler: null,

    textSelectionClosePreventionMouseUpHandler: null,

    textSelectionClosePreventionClickHandler: null,

    init() {
        this.$nextTick(() => {
            this.isWindowVisible = this.isOpen

            this.refreshScrollContainer()
            this.setUpTextSelectionClosePrevention()

            this.$watch('isOpen', () => {
                this.isWindowVisible = this.isOpen

                if (this.isOpen) {
                    this.refreshScrollContainer()
                    this.isRestoringScrollPosition = true
                    this.restoreScrollPositionAfterOpen()

                    return
                }

                this.storeScrollPosition(true)
            })

            if (this.isOpen) {
                this.isRestoringScrollPosition = true
                this.restoreScrollPositionAfterOpen()
            }
        })
    },

    refreshScrollContainer() {
        const scrollContainer = this.getScrollContainer()

        if (scrollContainer === this.scrollContainer) {
            return
        }

        if (this.scrollContainer && this.scrollListener) {
            this.scrollContainer.removeEventListener(
                'scroll',
                this.scrollListener,
            )
        }

        this.scrollContainer = scrollContainer
        this.setUpScrollPositionPersistence()
    },

    restoreScrollPositionAfterOpen() {
        this.$nextTick(() => {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    this.restoreScrollPosition()
                    this.isRestoringScrollPosition = false
                })
            })
        })
    },

    getScrollContainer() {
        const modalWindow = this.$el.querySelector('.fi-modal-window')

        if (
            this.$el.classList.contains('fi-modal-slide-over') ||
            this.$el.classList.contains('fi-modal-has-sticky-header') ||
            this.$el.classList.contains('fi-modal-has-sticky-footer')
        ) {
            return modalWindow
        }

        return this.$el.querySelector('.fi-modal-window-ctn') ?? modalWindow
    },

    setUpScrollPositionPersistence() {
        if (!id || !this.scrollContainer) {
            return
        }

        this.scrollListener = () => this.storeScrollPosition()

        this.scrollContainer.addEventListener('scroll', this.scrollListener, {
            passive: true,
        })
    },

    storeScrollPosition(force = false) {
        if (!force && this.isRestoringScrollPosition) {
            return
        }

        this.refreshScrollContainer()

        if (!id || !this.scrollContainer) {
            return
        }

        modalScrollPositions.set(id, {
            left: this.scrollContainer.scrollLeft,
            top: this.scrollContainer.scrollTop,
        })
    },

    restoreScrollPosition() {
        this.refreshScrollContainer()

        if (!id || !this.scrollContainer) {
            return
        }

        const scrollPosition = modalScrollPositions.get(id)

        if (!scrollPosition) {
            return
        }

        this.scrollContainer.scrollLeft = scrollPosition.left
        this.scrollContainer.scrollTop = scrollPosition.top
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

    close() {
        this.closeQuietly()

        this.$dispatch('modal-closed', { id })
    },

    closeQuietly() {
        this.refreshScrollContainer()
        this.storeScrollPosition(true)
        this.isRestoringScrollPosition = false
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
        this.refreshScrollContainer()
        this.storeScrollPosition(true)
        this.isRestoringScrollPosition = false

        if (this.scrollContainer && this.scrollListener) {
            this.scrollContainer.removeEventListener(
                'scroll',
                this.scrollListener,
            )
            this.scrollListener = null
        }

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
