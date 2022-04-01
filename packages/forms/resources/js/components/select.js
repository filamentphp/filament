import TomSelect from 'tom-select'

import 'tom-select/dist/css/tom-select.bootstrap5.css'

export default (Alpine) => {
    Alpine.data('selectFormComponent', ({
        getOptionLabelUsing,
        getOptionsUsing,
        getSearchResultsUsing,
        isAutofocused,
        isMultiple,
        hasDynamicOptions,
        maxItems,
        noSearchResultsMessage,
        options,
        placeholder,
        state,
    }) => {
        return {
            select: null,

            shouldUpdateState: true,

            state,

            init: function () {
                this.select = new TomSelect(this.$refs.input, {
                    loadThrottle: 1000,

                    options: this.transformOptions(options),

                    placeholder,

                    load: async (query, loadOptions) => {
                        if (query === undefined || query === null || query === '') {
                            loadOptions(this.transformOptions(await getOptionsUsing()))

                            return
                        }

                        loadOptions(this.transformOptions(await getSearchResultsUsing(query)))
                    },

                    onChange: (state) => {
                        this.shouldUpdateState = false
                        this.state = state
                    },

                    onDropdownOpen: async () => {
                        if (! hasDynamicOptions) {
                            return
                        }

                        this.select.clearOptions()
                        this.select.load()
                    },

                    onFocus: async () => {
                        this.select.open()
                    },

                    render: {

                        no_results: () => `<div class="no-results">${noSearchResultsMessage}</div>`,

                    },
                })

                if (maxItems) {
                    this.select.setMaxItems(maxItems)
                }

                this.refreshItems()

                if (isAutofocused) {
                    this.select.focus()
                }

                this.$watch('state', () => {
                    if (! this.shouldUpdateState) {
                        this.shouldUpdateState = true

                        return
                    }

                    this.refreshItems()
                })
            },

            refreshItems: function () {
                this.select.clear(true)

                if (isMultiple) {
                    this.state.forEach((selectedItem) => {
                        this.select.addItem(selectedItem, true)
                    })
                } else {
                    this.select.addItem(this.state, true)
                }

                this.select.refreshItems()
            },

            transformOptions: function (options) {
                let optionEntires = Object.entries(options)

                if (! optionEntires.length) {
                    return []
                }

                if (typeof optionEntires[0][1] === 'object') {
                    return options
                }

                return optionEntires.map((option) => ({ value: option[0], text: option[1] }))
            }
        }
    })
}
