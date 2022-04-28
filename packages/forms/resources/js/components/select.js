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
        maxItems,
        noSearchResultsMessage,
        options,
        placeholder,
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
                    loadingText: 'Loading...',
                    maxItemCount: maxItems ?? -1,
                    noResultsText: noSearchResultsMessage,
                    renderChoiceLimit: 50,
                    placeholderValue: placeholder,
                    searchChoices: ! hasDynamicSearchResults,
                    searchFields: ['label'],
                    searchResultLimit: 50,
                    shouldSort: false,
                })

                await this.refreshChoices({ withInitialOptions: true })

                if (isAutofocused) {
                    this.select.focus()
                }

                this.$refs.input.addEventListener('change', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.isStateBeingUpdated = true
                    this.state = this.select.getValue(true)
                    this.isStateBeingUpdated = false
                })

                if (hasDynamicOptions) {
                    this.$refs.input.addEventListener('showDropdown', async (event) => {
                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: 'Loading...', disabled: true }],
                        )

                        await this.refreshChoices()
                    })
                }

                if (hasDynamicSearchResults) {
                    this.$refs.input.addEventListener('search', async (event) => {
                        this.isSearching = true

                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: 'Searching...', disabled: true }],
                        )
                    })

                    this.$refs.input.addEventListener('search', Alpine.debounce(async (event) => {
                        await this.refreshChoices({
                            search: event.detail.value,
                        })

                        this.isSearching = false
                    }, 1000))
                }

                this.$watch('state', async () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    await this.refreshChoices({ withInitialOptions: ! hasDynamicOptions })
                })
            },

            refreshChoices: async function (config = {}) {
                await this.select.setChoices(
                    await this.getChoices(config),
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
                let selection = isMultiple ?
                    ((this.state ?? []).map((value) => value?.toString())) :
                    [this.state?.toString()]

                return Object.entries(options)
                    .map((option) => ({
                        label: option[1],
                        selected: selection.includes(option[0]?.toString()),
                        value: option[0],
                    }))
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
