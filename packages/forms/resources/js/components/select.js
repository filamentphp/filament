import { Select } from '../../../../support/resources/js/utilities/select.js'

export default function selectFormComponent({
    canOptionLabelsWrap,
    canSelectPlaceholder,
    isHtmlAllowed,
    getOptionLabelUsing,
    getOptionLabelsUsing,
    getOptionsUsing,
    getSearchResultsUsing,
    initialOptionLabel,
    initialOptionLabels,
    initialState,
    isAutofocused,
    isDisabled,
    isMultiple,
    isSearchable,
    hasDynamicOptions,
    hasDynamicSearchResults,
    livewireId,
    loadingMessage,
    maxItems,
    maxItemsMessage,
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
                element: this.$refs.select,
                options,
                placeholder,
                state: this.state,
                canOptionLabelsWrap,
                canSelectPlaceholder,
                initialOptionLabel,
                initialOptionLabels,
                initialState,
                isHtmlAllowed,
                isAutofocused,
                isDisabled,
                isMultiple,
                isSearchable,
                getOptionLabelUsing,
                getOptionLabelsUsing,
                getOptionsUsing,
                getSearchResultsUsing,
                hasDynamicOptions,
                hasDynamicSearchResults,
                searchPrompt,
                searchDebounce,
                loadingMessage,
                searchingMessage,
                noSearchResultsMessage,
                maxItems,
                maxItemsMessage,
                optionsLimit,
                position,
                searchableOptionFields,
                livewireId,
                statePath,
                onStateChange: (newState) => {
                    this.state = newState
                },
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
