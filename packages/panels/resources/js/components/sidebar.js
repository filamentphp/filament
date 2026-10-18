export default ({
    hasTopNavigation,
    isSidebarCollapsibleOnDesktop,
    isSidebarFullyCollapsibleOnDesktop,
}) => ({
    get isHidden() {
        if (this.$store.sidebar.isMobile) {
            return !this.$store.sidebar.isOpen
        }

        return (
            hasTopNavigation ||
            (isSidebarFullyCollapsibleOnDesktop &&
                !isSidebarCollapsibleOnDesktop &&
                !this.$store.sidebar.isOpen)
        )
    },

    focusInitialNavigation() {
        if (!this.$store.sidebar.isOpenMobile) {
            return
        }

        this.$nextTick(() => {
            if (!this.$store.sidebar.isOpenMobile || !this.$el.isConnected) {
                return
            }

            const elements = this.getFocusableElements()
            const target =
                elements.find((element) => element.closest('nav')) ??
                elements[0]

            target?.focus()
        })
    },

    getFocusableElements() {
        return this.$focus
            .all()
            .filter(
                (element) =>
                    element.tabIndex >= 0 &&
                    element.getClientRects().length &&
                    getComputedStyle(element).visibility === 'visible',
            )
    },

    handleTab(event) {
        if (
            !this.$store.sidebar.isOpenMobile ||
            event.defaultPrevented ||
            event.target.closest('[aria-modal=true]')
        ) {
            return
        }

        event.preventDefault()
        event.stopPropagation()

        const focus = this.$focus.within(this.getFocusableElements()).wrap()
        const target = event.shiftKey
            ? (focus.getPrevious() ?? focus.getLast())
            : (focus.getNext() ?? focus.getFirst())

        target?.focus()
    },

    handleEscape(event) {
        if (
            !this.$store.sidebar.isOpenMobile ||
            event.defaultPrevented ||
            event.target.closest('[aria-modal=true]') ||
            document.querySelector('.fi-modal-open[aria-modal="true"]')
        ) {
            return
        }

        event.stopPropagation()
        this.$store.sidebar.close()
    },
})
