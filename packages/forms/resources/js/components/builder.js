export default function builderBlockPickerFormComponent() {
    return {
        search: '',

        blockLabels: [],

        observer: null,

        init() {
            const syncBlockLabels = () => {
                this.blockLabels = Array.from(
                    this.$root.querySelectorAll('[data-block-label]'),
                    (element) => element.dataset.blockLabel,
                )
            }

            syncBlockLabels()

            this.observer = new MutationObserver(syncBlockLabels)
            this.observer.observe(this.$root, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['data-block-label'],
            })
        },

        destroy() {
            this.observer?.disconnect()
        },

        clearSearch() {
            this.search = ''

            // With a debounce, `search` may still be empty while the input already
            // has text, so clear the input directly instead of relying on `x-model`.
            if (this.$refs.searchInput) {
                this.$refs.searchInput.value = ''
            }
        },

        handleEscape(event) {
            if (!this.search && !this.$refs.searchInput?.value) {
                return
            }

            // Keep the picker open and clear the search instead.
            event.preventDefault()

            this.clearSearch()
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

            return !this.blockLabels.some((label) =>
                label.includes(this.search.toLowerCase()),
            )
        },
    }
}
