export default function builderBlockPickerFormComponent({ blockLabels }) {
    return {
        search: '',

        clearSearch() {
            this.search = ''
        },

        handleSearchEscape(event) {
            if (!this.search) {
                return
            }

            this.search = ''

            event.stopPropagation()
        },

        isBlockVisible(blockIndex) {
            if (!this.search) {
                return true
            }

            return blockLabels[blockIndex].includes(this.search.toLowerCase())
        },

        get hasNoSearchResults() {
            return (
                this.search &&
                !blockLabels.some((blockLabel) =>
                    blockLabel.includes(this.search.toLowerCase()),
                )
            )
        },
    }
}
