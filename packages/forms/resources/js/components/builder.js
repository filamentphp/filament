export default function builderBlockPickerFormComponent() {
    return {
        search: '',

        init() {
            // Registered before the floating UI plugin's own window `keydown`
            // listener, so a nonempty search is cleared before the panel closes.
            this.handleKeydownCapture = (event) => {
                if (event.key !== 'Escape') {
                    return
                }

                if (!this.$root.contains(event.target)) {
                    return
                }

                if (this.search) {
                    this.search = ''

                    event.stopImmediatePropagation()

                    return
                }

                // Let the panel close, then return focus to its trigger.
                const trigger = this.$root
                    .closest('.fi-dropdown')
                    ?.querySelector(
                        '.fi-dropdown-trigger button, .fi-dropdown-trigger a, .fi-dropdown-trigger [tabindex]',
                    )

                setTimeout(() => trigger?.focus())
            }

            window.addEventListener('keydown', this.handleKeydownCapture, true)
        },

        destroy() {
            window.removeEventListener(
                'keydown',
                this.handleKeydownCapture,
                true,
            )
        },

        clearSearch() {
            this.search = ''
        },

        isBlockVisible(blockElement) {
            if (!this.search) {
                return true
            }

            return blockElement.dataset.blockLabel.includes(
                this.search.toLowerCase(),
            )
        },

        get hasNoSearchResults() {
            if (!this.search) {
                return false
            }

            return !Array.from(
                this.$root.querySelectorAll('[data-block-label]'),
            ).some((blockElement) => this.isBlockVisible(blockElement))
        },
    }
}
