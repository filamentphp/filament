export default function builderBlockPickerFormComponent() {
    return {
        search: '',

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

            return !Array.from(
                this.$root.querySelectorAll('[data-block-label]'),
            ).some((blockElement) => this.isBlockVisible(blockElement))
        },
    }
}
