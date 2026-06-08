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
        isMultiple,

        select: null,

        state,

        wireSelect: null,

        init() {
            this.wireSelect = this.$root
                .closest('.fi-fo-select')
                ?.querySelector('[data-wire-select]')

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

                    this.syncToWireSelect(newState)
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

            this.syncToWireSelect(this.state, false)

            this.$wire.watch(statePath, (newState) => {
                if (
                    JSON.stringify(newState) !== JSON.stringify(this.state)
                ) {
                    this.state = newState
                }
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

        normalizeStateToValues(newState) {
            if (this.isMultiple) {
                if (! Array.isArray(newState)) {
                    return []
                }

                return newState.map((value) => String(value))
            }

            if (
                newState === null ||
                newState === undefined ||
                newState === ''
            ) {
                return []
            }

            return [String(newState)]
        },

        ensureWireSelectOptions(newState) {
            if (! this.wireSelect) {
                return
            }

            this.normalizeStateToValues(newState).forEach((value) => {
                if (
                    ! [...this.wireSelect.options].some(
                        (option) => option.value === value,
                    )
                ) {
                    const option = document.createElement('option')

                    option.value = value

                    this.wireSelect.appendChild(option)
                }
            })
        },

        syncToWireSelect(newState, shouldDispatch = true) {
            if (! this.wireSelect) {
                return
            }

            const values = this.normalizeStateToValues(newState)

            this.ensureWireSelectOptions(newState)

            if (this.isMultiple) {
                ;[...this.wireSelect.options].forEach((option) => {
                    option.selected = values.includes(option.value)
                })
            } else {
                this.wireSelect.value = values[0] ?? ''
            }

            if (shouldDispatch) {
                this.wireSelect.dispatchEvent(
                    new Event('input', { bubbles: true }),
                )
            }
        },

        destroy() {
            if (this.select) {
                this.select.destroy()
                this.select = null
            }
        },
    }
}
