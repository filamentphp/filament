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
        hasDynamicSearchResults,
        loadingMessage,
        maxItems,
        noSearchResultsMessage,
        options,
        placeholder,
        searchingMessage,
        state,
    }) => {
        return {
            isSearching: false,

            select: null,

            selectedOptions: [],

            isStateBeingUpdated: false,

            state,

            init: async function () {
                this.select = new Choices(this.$refs.input, {
                    allowHTML: false,
                    duplicateItemsAllowed: false,
                    itemSelectText: '',
                    loadingText: loadingMessage,
                    maxItemCount: maxItems ?? -1,
                    noResultsText: noSearchResultsMessage,
                    renderChoiceLimit: 50,
                    placeholderValue: placeholder,
                    searchChoices: ! hasDynamicSearchResults,
                    searchFields: ['label'],
                    searchResultLimit: 50,
                })

                await this.refreshChoices({ withInitialOptions: true })
                this.select.setChoiceByValue(this.transformState(this.state))

                if (isAutofocused) {
                    this.select.showDropdown()
                }

                this.$refs.input.addEventListener('change', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.isStateBeingUpdated = true
                    this.state = this.select.getValue(true)
                    this.$nextTick(() => this.isStateBeingUpdated = false)
                })

                if (hasDynamicOptions) {
                    this.$refs.input.addEventListener('showDropdown', async () => {
                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: loadingMessage, disabled: true }],
                        )

                        await this.refreshChoices()
                    })
                }

                if (hasDynamicSearchResults) {
                    this.$refs.input.addEventListener('search', async (event) => {
                        let search = event.detail.value?.trim()

                        if (search === null) {
                            return
                        }

                        if (search === '') {
                            return
                        }

                        if (search === undefined) {
                            return
                        }

                        this.isSearching = true

                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: searchingMessage, disabled: true }],
                        )
                    })

                    this.$refs.input.addEventListener('search', Alpine.debounce(async (event) => {
                        await this.refreshChoices({
                            search: event.detail.value?.trim(),
                        })

                        this.isSearching = false
                    }, 1000))
                }

                this.$watch('state', async () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.select.clearStore()

                    await this.refreshChoices({
                        withInitialOptions: ! hasDynamicOptions,
                    })

                    this.select.setChoiceByValue(this.transformState(this.state))
                })
            },

            refreshChoices: async function (config = {}) {
                const choices = await this.getChoices(config)

                await this.select.setChoices(
                    choices,
                    'value',
                    'label',
                    true,
                )
            },

            getChoices: async function (config = {}) {
                return this.transformOptions({
                    ...await this.getOptions(config),
                    ...await this.getMissingOptions(options),
                })
            },

            getOptions: async function ({ search, withInitialOptions }) {
                if (withInitialOptions) {
                    return options
                }

                if ((search !== '') && (search !== null) && (search !== undefined)) {
                    return await getSearchResultsUsing(search)
                }

                return await getOptionsUsing()
            },

            transformOptions: function (options) {
                return Object.entries(options)
                    .map((option) => ({
                        label: option[1],
                        value: option[0],
                    }))
            },

            transformState: function (state) {
                if (! isMultiple) {
                    return state.toString()
                }

                return (state ?? []).map((item) => item.toString())
            },

            getMissingOptions: async function (options) {
                if (this.state === null) {
                    return {}
                }

                if (this.state === '') {
                    return {}
                }

                if (this.state === undefined) {
                    return {}
                }

                if (this.state === []) {
                    return {}
                }

                if (this.state === {}) {
                    return {}
                }

                if (isMultiple) {
                    if (! this.state.some((value) => ! value in options)) {
                        return {}
                    }

                    return await getOptionLabelsUsing()
                }

                if (this.state in options) {
                    return options
                }

                let missingOptions = {}
                missingOptions[this.state] = await getOptionLabelUsing()
                return missingOptions
            },
        }
    })
}
