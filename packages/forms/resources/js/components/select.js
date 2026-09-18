import { Select } from '../../../../support/resources/js/utilities/select.js'

export default function selectFormComponent({
    canOptionLabelsWrap,
    canSelectPlaceholder,
    clearButtonLabel,
    getOptionLabelUsing,
    getOptionLabelsUsing,
    getOptionsUsing,
    getSearchResultsUsing,
    hasDynamicOptions,
    hasDynamicSearchResults,
    hasInitialNoOptionsMessage,
    id,
    initialOptionLabel,
    initialOptionLabels,
    initialState,
    isAutofocused,
    isDisabled,
    isHtmlAllowed,
    isMultiple,
    isReorderable,
    isSearchable,
    livewireId,
    loadingMessage,
    maxItems,
    maxItemsMessage,
    noOptionsMessage,
    noSearchResultsMessage,
    options,
    optionsLimit,
    placeholder,
    position,
    removeButtonLabel,
    searchDebounce,
    searchingMessage,
    searchLabel,
    searchPrompt,
    searchableOptionFields,
    state,
    statePath,
}) {
    return {
        select: null,

        state,

        init() {
            this.select = new Select({
                canOptionLabelsWrap,
                canSelectPlaceholder,
                clearButtonLabel,
                element: this.$refs.select,
                getOptionLabelUsing,
                getOptionLabelsUsing,
                getOptionsUsing,
                getSearchResultsUsing,
                hasDynamicOptions,
                hasDynamicSearchResults,
                hasInitialNoOptionsMessage,
                id,
                initialOptionLabel,
                initialOptionLabels,
                initialState,
                isAutofocused,
                isDisabled,
                isHtmlAllowed,
                isMultiple,
                isReorderable,
                isSearchable,
                livewireId,
                loadingMessage,
                maxItems,
                maxItemsMessage,
                noOptionsMessage,
                noSearchResultsMessage,
                onStateChange: (newState) => {
                    this.state = newState
                },
                options,
                optionsLimit,
                placeholder,
                position,
                removeButtonLabel,
                searchableOptionFields,
                searchDebounce,
                searchingMessage,
                searchLabel,
                searchPrompt,
                state: this.state,
                statePath,
            })

            this.$watch('state', (newState) => {
                this.$nextTick(() => {
                    if (this.select && this.select.state !== newState) {
                        this.select.state = newState
                        this.select.updateSelectedDisplay()
                        this.select.renderOptions()
                    }
                })
            })
        },

        destroy() {
            if (this.select) {
                this.select.destroy()
                this.select = null
            }
        },
    }
}
