export default () => ({
    toggle(event) {
        const wasOpen =
            this.$refs.panel?.hasAttribute('data-open') ||
            this.$refs.panel?.style.display !== 'none'
        this.$refs.panel?.toggle(event)

        // If dropdown was closed and is now opening, focus first element
        if (!wasOpen) {
            this.$nextTick(() => this.focusFirstElement())
        }
    },

    open(event) {
        this.$refs.panel?.open(event)
        this.$nextTick(() => this.focusFirstElement())
    },

    close(event) {
        this.$refs.panel?.close(event)
    },

    handleItemClick(event) {
        // Only close dropdown when clicking on actionable items (buttons, links, etc.)
        const clickedElement = event.target.closest(
            'button, [href], [role="button"], [role="menuitem"]',
        )

        if (clickedElement && !clickedElement.disabled) {
            // Small delay to allow the action to be processed first
            this.$nextTick(() => {
                this.close(event)
            })
        }
    },

    handleKeydown(event) {
        // Handle arrow key navigation within the dropdown
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault()
            this.navigateItems(event.key === 'ArrowDown')
        }
        // Handle Tab key to exit dropdown and focus next element outside
        else if (event.key === 'Tab') {
            this.close(event)
            // Let the default Tab behavior continue to next focusable element
        }
        // Handle Home key to focus first element
        else if (event.key === 'Home') {
            event.preventDefault()
            this.focusFirstElement()
        }
        // Handle End key to focus last element
        else if (event.key === 'End') {
            event.preventDefault()
            this.focusLastElement()
        }
    },

    navigateItems(down = true) {
        if (!this.$refs.panel) return

        const focusableElements = this.getFocusableElements()
        if (focusableElements.length === 0) return

        const currentIndex = focusableElements.findIndex(
            (el) => el === document.activeElement,
        )
        let nextIndex

        if (currentIndex === -1) {
            // No element is focused, focus first or last based on direction
            nextIndex = down ? 0 : focusableElements.length - 1
        } else {
            // Navigate to next/previous item with wrapping
            if (down) {
                nextIndex =
                    currentIndex + 1 >= focusableElements.length
                        ? 0
                        : currentIndex + 1
            } else {
                nextIndex =
                    currentIndex - 1 < 0
                        ? focusableElements.length - 1
                        : currentIndex - 1
            }
        }

        focusableElements[nextIndex].focus()
    },

    getFocusableElements() {
        if (!this.$refs.panel) return []

        return Array.from(
            this.$refs.panel.querySelectorAll(
                'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"]):not([disabled]), [contenteditable="true"]',
            ),
        )
    },

    focusFirstElement() {
        const focusableElements = this.getFocusableElements()

        if (focusableElements.length > 0) {
            focusableElements[0].focus()
        }
    },

    focusLastElement() {
        const focusableElements = this.getFocusableElements()

        if (focusableElements.length > 0) {
            focusableElements[focusableElements.length - 1].focus()
        }
    },
})
