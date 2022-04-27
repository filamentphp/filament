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

            state,

            init: async function () {
                this.select = new TomSelect(this.$refs.input, {
                    loadThrottle: 500,

                    options: await this.transformOptions(options),

                    placeholder,

                    shouldUpdateState: true,

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

                        this.shouldUpdateState = true
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

                if (this.state === null) {
                    this.shouldUpdateState = false

                    this.state = isMultiple ? [] : ''

                    this.shouldUpdateState = true
                }

                if (maxItems) {
                    this.select.setMaxItems(maxItems)
                }

                this.refreshItems()

                if (isAutofocused) {
                    this.select.focus()
                }

                this.$watch('state', async () => {
                    if (! this.shouldUpdateState) {
                        return
                    }

                    await this.refreshOptions()

                    this.refreshItems()
                })
            },

            refreshItems: function () {
                this.select.clear(true)

                if (isMultiple) {
                    Object.values(this.state ?? {}).forEach((item) => {
                        this.select.addItem(item, true)
                    })
                } else {
                    this.select.addItem(this.state, true)
                    this.select.refreshItems()
                }
            },

            refreshOptions: async function () {
                this.select.clearOptions()
                this.select.addOptions(await this.transformOptions(await getOptionsUsing()))
                this.select.refreshOptions()
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

                return optionEntires
                    .map((option) => ({
                        value: option[0],
                        text: option[1],
                    }))
            },

            loadMissingOptions: async function (options) {
                if (this.state === '' || this.state === null || this.state === undefined) {
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
