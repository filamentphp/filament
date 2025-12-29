import { Select } from '../../../../support/resources/js/utilities/select.js'

export default function selectFormComponent({
    canOptionLabelsWrap,
    canSelectPlaceholder,
    getOptionLabelUsing,
    getOptionLabelsUsing,
    getOptionsUsing,
    getSearchResultsUsing,
    hasDynamicOptions,
    hasDynamicSearchResults,
    hasInitialNoOptionsMessage,
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
    searchDebounce,
    searchingMessage,
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
                element: this.$refs.select,
                getOptionLabelUsing,
                getOptionLabelsUsing,
                getOptionsUsing,
                getSearchResultsUsing,
                hasDynamicOptions,
                hasDynamicSearchResults,
                hasInitialNoOptionsMessage,
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
                searchableOptionFields,
                searchDebounce,
                searchingMessage,
                searchPrompt,
                state: this.state,
                statePath,
            })

            this.$watch('state', (newState) => {
                if (this.select && this.select.state !== newState) {
                    this.select.state = newState
                    this.select.updateSelectedDisplay()
                    this.select.renderOptions()
                }
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
