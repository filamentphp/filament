import {
    Select,
    createSelectOptionRequestHandler,
} from '../../../../support/resources/js/utilities/select.js'

export default function selectFormComponent({
    canOptionLabelsWrap,
    canSelectPlaceholder,
    clearButtonLabel,
    errorMessage,
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
    const optionRequests = createSelectOptionRequestHandler()

    return {
        select: null,

        accessibilityObserver: null,

        state,

        init() {
            this.select = new Select({
                canOptionLabelsWrap,
                canSelectPlaceholder,
                clearButtonLabel,
                element: this.$refs.select,
                errorMessage,
                getOptionLabelUsing,
                getOptionLabelsUsing,
                getOptionsUsing: optionRequests.wrap(getOptionsUsing),
                getSearchResultsUsing: optionRequests.wrap(
                    getSearchResultsUsing,
                ),
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

            const attributesElement = this.$el.previousElementSibling
            if (attributesElement?.hasAttribute('data-select-accessibility')) {
                const syncAccessibility = () =>
                    this.select?.setAccessibilityAttributes(
                        JSON.parse(
                            attributesElement.dataset.selectAccessibility,
                        ),
                    )
                syncAccessibility()
                this.accessibilityObserver = new MutationObserver(
                    syncAccessibility,
                )
                this.accessibilityObserver.observe(attributesElement, {
                    attributes: true,
                    attributeFilter: ['data-select-accessibility'],
                })
            }

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
            this.accessibilityObserver?.disconnect()
            if (this.select) {
                this.select.destroy()
                this.select = null
            }
            optionRequests.destroy()
        },
    }
}
