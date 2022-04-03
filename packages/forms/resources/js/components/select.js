import TomSelect from 'tom-select'

import '../../css/components/select.css'

export default (Alpine) => {
    Alpine.data('selectFormComponent', ({
        getOptionLabelUsing,
        getOptionLabelsUsing,
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

            init: async function () {
                this.select = new TomSelect(this.$refs.input, {
                    loadThrottle: 1000,

                    options: await this.transformOptions(options),

                    placeholder,

                    load: async (query, loadOptions) => {
                        if (query === undefined || query === null || query === '') {
                            loadOptions(await this.transformOptions(await getOptionsUsing()))

                            return
                        }

                        loadOptions(await this.transformOptions(await getSearchResultsUsing(query)))
                    },

                    onChange: (items) => {
                        this.shouldUpdateState = false

                        if (Array.isArray(items)) {
                            items = { ...items }
                        }

                        this.state = items
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
                    this.state.forEach((item) => {
                        this.select.addItem(item, false)
                    })
                } else {
                    this.select.addItem(this.state, true)
                }

                this.select.refreshItems()
            },

            transformOptions: async function (options) {
                options = await this.loadMissingOptions(options)

                let optionEntires = Object.entries(options)

                if (! optionEntires.length) {
                    return []
                }

                if (typeof optionEntires[0][1] === 'object') {
                    return options
                }

                return optionEntires.map((option) => ({ value: option[0], text: option[1] }))
            },

            loadMissingOptions: async function (options) {
                if (this.state === null || this.state === undefined) {
                    return options
                }

                if (isMultiple) {
                    if (Object.values(this.state ?? {}).every((item) => item in options)) {
                        return options
                    }

                    return {
                        ...options,
                        ...await getOptionLabelsUsing(),
                    }
                }

                if (this.state in options) {
                    return options
                }

                options[this.state] = await getOptionLabelUsing()

                return options
            },
        }
    })
}
