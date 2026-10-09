import collapsibleItem from '../utils/collapsible-item.js'
import reordering from '../utils/reordering.js'

export default function builderFormComponent({ statePath, ...configuration }) {
    return {
        ...reordering(configuration),

        builderFormComponentBlockPicker,

        collapseAll() {
            this.$dispatch('builder-collapse', statePath)
        },

        expandAll() {
            this.$dispatch('builder-expand', statePath)
        },

        editItem(item) {
            this.$wire.mountAction(
                'edit',
                { item },
                { schemaComponent: configuration.schemaComponent },
            )
        },

        item(configuration) {
            return collapsibleItem({ statePath, ...configuration })
        },
    }
}

function builderFormComponentBlockPicker() {
    return {
        search: '',

        blockLabels: [],

        observer: null,

        dropdownPanel: null,

        dropdownOpenedListener: null,

        dropdownEscapeListener: null,

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

            this.setUpDropdownAutofocus()

            this.dropdownEscapeListener = (event) => {
                this.handleEscape(event)
                event.stopPropagation()
            }
            this.$root.addEventListener(
                'dropdown-escape',
                this.dropdownEscapeListener,
            )
        },

        setUpDropdownAutofocus() {
            this.dropdownPanel = this.$root.closest('.fi-dropdown-panel')
            this.dropdownOpenedListener = () => this.autofocusSearch()

            this.dropdownPanel?.addEventListener(
                'dropdown-opened',
                this.dropdownOpenedListener,
            )

            const dropdownTrigger = this.dropdownPanel
                ?.closest('.fi-dropdown')
                ?.querySelector(
                    ':scope > .fi-dropdown-trigger button, :scope > .fi-dropdown-trigger a, :scope > .fi-dropdown-trigger [tabindex]',
                )

            if (
                this.dropdownPanel?.style.display !== 'block' ||
                document.activeElement !== dropdownTrigger
            ) {
                return
            }

            this.autofocusSearch()
        },

        autofocusSearch() {
            if (
                !this.$root.isConnected ||
                this.dropdownPanel?.style.display !== 'block'
            ) {
                return
            }

            this.clearSearch()
            this.$refs.searchInput?.focus()
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

        destroy() {
            this.observer?.disconnect()

            this.dropdownPanel?.removeEventListener(
                'dropdown-opened',
                this.dropdownOpenedListener,
            )
            this.$root.removeEventListener(
                'dropdown-escape',
                this.dropdownEscapeListener,
            )
        },
    }
}
