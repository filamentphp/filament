import Choices from 'choices.js'

import '../../css/components/select.css'

export default (Alpine) => {
    Alpine.data(
        'selectFormComponent',
        ({
            isHtmlAllowed,
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
            optionsLimit,
            placeholder,
            searchingMessage,
            searchPrompt,
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
                        allowHTML: isHtmlAllowed,
                        duplicateItemsAllowed: false,
                        itemSelectText: '',
                        loadingText: loadingMessage,
                        maxItemCount: maxItems ?? -1,
                        noChoicesText: searchPrompt,
                        noResultsText: noSearchResultsMessage,
                        placeholderValue: placeholder,
                        removeItemButton: true,
                        renderChoiceLimit: optionsLimit,
                        searchFields: ['label'],
                        searchResultLimit: optionsLimit,
                        shouldSort: false,
                    })

                    await this.refreshChoices({ withInitialOptions: true })

                    if (![null, undefined, ''].includes(this.state)) {
                        this.select.setChoiceByValue(
                            this.formatState(this.state),
                        )
                    }

                    this.refreshPlaceholder()

                    if (isAutofocused) {
                        this.select.showDropdown()
                    }

                    this.$refs.input.addEventListener('change', () => {
                        this.refreshPlaceholder()

                        if (this.isStateBeingUpdated) {
                            return
                        }

                        this.isStateBeingUpdated = true
                        this.state = this.select.getValue(true) ?? null
                        this.$nextTick(() => (this.isStateBeingUpdated = false))
                    })

                    if (hasDynamicOptions) {
                        this.$refs.input.addEventListener(
                            'showDropdown',
                            async () => {
                                this.select.clearChoices()
                                await this.select.setChoices([
                                    {
                                        value: '',
                                        label: loadingMessage,
                                        disabled: true,
                                    },
                                ])

                                await this.refreshChoices()
                            },
                        )
                    }

                    if (hasDynamicSearchResults) {
                        this.$refs.input.addEventListener(
                            'search',
                            async (event) => {
                                let search = event.detail.value?.trim()

                                if ([null, undefined, ''].includes(search)) {
                                    return
                                }

                                this.isSearching = true

                                this.select.clearChoices()
                                await this.select.setChoices([
                                    {
                                        value: '',
                                        label: searchingMessage,
                                        disabled: true,
                                    },
                                ])
                            },
                        )

                        this.$refs.input.addEventListener(
                            'search',
                            Alpine.debounce(async (event) => {
                                await this.refreshChoices({
                                    search: event.detail.value?.trim(),
                                })

                                this.isSearching = false
                            }, 1000),
                        )
                    }

                    this.$watch('state', async () => {
                        this.refreshPlaceholder()

                        if (this.isStateBeingUpdated) {
                            return
                        }

                        const choices = await this.getChoices({
                            withInitialOptions: !hasDynamicOptions,
                        })

                        this.select.clearStore()

                        this.setChoices(choices)

                        if (![null, undefined, ''].includes(this.state)) {
                            this.select.setChoiceByValue(
                                this.formatState(this.state),
                            )
                        }
                    })
                },

                refreshChoices: async function (config = {}) {
                    this.setChoices(await this.getChoices(config))
                },

                setChoices: function (choices) {
                    this.select.setChoices(choices, 'value', 'label', true)
                },

                getChoices: async function (config = {}) {
                    const options = await this.getOptions(config)

                    return this.transformOptionsIntoChoices({
                        ...options,
                        ...(await this.getMissingOptions(options)),
                    })
                },

                getOptions: async function ({ search, withInitialOptions }) {
                    if (withInitialOptions) {
                        return options
                    }

                    if (
                        search !== '' &&
                        search !== null &&
                        search !== undefined
                    ) {
                        return await getSearchResultsUsing(search)
                    }

                    return await getOptionsUsing()
                },

                transformOptionsIntoChoices: function (options) {
                    return Object.entries(options).map(([value, label]) => ({
                        label,
                        value,
                    }))
                },

                refreshPlaceholder: function () {
                    if (isMultiple) {
                        return
                    }

                    this.select._renderItems()

                    if (![null, undefined, ''].includes(this.state)) {
                        return
                    }

                    this.$el.querySelector(
                        '.choices__list--single',
                    ).innerHTML = `<div class="choices__placeholder choices__item">${placeholder}</div>`
                },

                formatState: function (state) {
                    if (isMultiple) {
                        return (state ?? []).map((item) => item?.toString())
                    }

                    return state?.toString()
                },

                getMissingOptions: async function (options) {
                    if ([null, undefined, '', [], {}].includes(this.state)) {
                        return {}
                    }

                    if (!options.length) {
                        options = {}
                    }

                    if (isMultiple) {
                        if (this.state.every((value) => value in options)) {
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
        },
    )
}
