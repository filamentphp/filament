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
            maxItemsMessage,
            noSearchResultsMessage,
            options,
            optionsLimit,
            placeholder,
            position,
            isPlaceholderSelectionDisabled,
            searchDebounce,
            searchingMessage,
            searchPrompt,
            searchableOptionFields,
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
                        maxItemText: (maxItemCount) =>
                            window.pluralize(maxItemsMessage, maxItemCount, {
                                count: maxItemCount,
                            }),
                        noChoicesText: searchPrompt,
                        noResultsText: noSearchResultsMessage,
                        placeholderValue: placeholder,
                        position: position ?? 'auto',
                        removeItemButton: !isPlaceholderSelectionDisabled,
                        renderChoiceLimit: optionsLimit,
                        searchFields: searchableOptionFields ?? ['label'],
                        searchPlaceholderValue: searchPrompt,
                        searchResultLimit: optionsLimit,
                        shouldSort: false,
                        searchFloor: hasDynamicSearchResults ? 0 : 1,
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
                                        label: loadingMessage,
                                        value: '',
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

                                this.isSearching = true

                                this.select.clearChoices()
                                await this.select.setChoices([
                                    {
                                        label: [null, undefined, ''].includes(
                                            search,
                                        )
                                            ? loadingMessage
                                            : searchingMessage,
                                        value: '',
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
                            }, searchDebounce),
                        )
                    }

                    this.$watch('state', async () => {
                        this.refreshPlaceholder()

                        if (this.isStateBeingUpdated) {
                            return
                        }

                        await this.refreshChoices({
                            withInitialOptions: !hasDynamicOptions,
                        })
                    })
                },

                refreshChoices: async function (config = {}) {
                    const choices = await this.getChoices(config)

                    this.select.clearStore()

                    this.refreshPlaceholder()

                    this.setChoices(choices)

                    if (![null, undefined, ''].includes(this.state)) {
                        this.select.setChoiceByValue(
                            this.formatState(this.state),
                        )
                    }
                },

                setChoices: function (choices) {
                    this.select.setChoices(choices, 'value', 'label', true)
                },

                getChoices: async function (config = {}) {
                    const existingOptions = await this.getOptions(config)

                    return existingOptions.concat(
                        await this.getMissingOptions(existingOptions),
                    )
                },

                getOptions: async function ({ search, withInitialOptions }) {
                    if (withInitialOptions) {
                        return options
                    }

                    let results = []

                    if (
                        search !== '' &&
                        search !== null &&
                        search !== undefined
                    ) {
                        results = await getSearchResultsUsing(search)
                    } else {
                        results = await getOptionsUsing()
                    }

                    return results.map((option) =>
                        this.state.includes(option.value)
                            ? ((option) => {
                                  option.selected = true

                                  return option
                              })(option)
                            : option,
                    )
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

                getMissingOptions: async function (existingOptions) {
                    let state = this.formatState(this.state)

                    if ([null, undefined, '', [], {}].includes(state)) {
                        return {}
                    }

                    const existingOptionValues = new Set(
                        existingOptions.length
                            ? existingOptions.map((option) => option.value)
                            : [],
                    )

                    if (isMultiple) {
                        if (
                            state.every((value) =>
                                existingOptionValues.has(value),
                            )
                        ) {
                            return {}
                        }

                        return await getOptionLabelsUsing()
                    }

                    if (existingOptionValues.has(state)) {
                        return existingOptionValues
                    }

                    return [
                        {
                            label: await getOptionLabelUsing(),
                            value: state,
                            selected: true,
                        },
                    ]
                },
            }
        },
    )
}
