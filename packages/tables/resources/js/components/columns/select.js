import { Select } from '../../../../../support/resources/js/utilities/select.js'
import editableTableColumn from '../../utilities/editable-table-column.js'

export default function selectTableColumn({
    accessibilityAttributes,
    ariaLabel,
    canOptionLabelsWrap,
    canSelectPlaceholder,
    clearButtonLabel,
    errorMessage,
    getOptionLabelUsing,
    getOptionsUsing,
    getSearchResultsUsing,
    hasDynamicOptions,
    hasDynamicSearchResults,
    hasInitialNoOptionsMessage,
    initialOptionLabel,
    isDisabled,
    isHtmlAllowed,
    isNative,
    isSearchable,
    loadingMessage,
    messages,
    name,
    noOptionsMessage,
    noSearchResultsMessage,
    options,
    optionsLimit,
    placeholder,
    position,
    recordKey,
    searchableOptionFields,
    searchDebounce,
    searchingMessage,
    searchLabel,
    searchPrompt,
    state,
}) {
    const editableColumn = editableTableColumn({
        name,
        recordKey,
        state,
        messages,
    })

    return {
        ...editableColumn,

        select: null,

        init() {
            if (!isNative) {
                this.select = new Select({
                    accessibilityAttributes,
                    ariaLabel,
                    canOptionLabelsWrap,
                    canSelectPlaceholder,
                    clearButtonLabel,
                    element: this.$refs.select,
                    errorMessage,
                    getOptionLabelUsing,
                    getOptionsUsing,
                    getSearchResultsUsing,
                    hasDynamicOptions,
                    hasDynamicSearchResults,
                    hasInitialNoOptionsMessage,
                    initialOptionLabel,
                    isDisabled,
                    isHtmlAllowed,
                    isSearchable,
                    loadingMessage,
                    noOptionsMessage,
                    noSearchResultsMessage,
                    onStateChange: (newState) => {
                        this.clearError()
                        this.state = newState
                    },
                    options,
                    optionsLimit,
                    placeholder,
                    position,
                    searchableOptionFields,
                    searchDebounce,
                    searchingMessage,
                    searchLabel,
                    searchPrompt,
                    state: this.state,
                })

                const syncAccessibilityAttributes = () => {
                    this.select.setAccessibilityAttributes(
                        this.getAccessibilityAttributes(
                            accessibilityAttributes,
                        ),
                    )

                    this.select.selectButton.setAttribute(
                        'aria-disabled',
                        this.isLoading || this.select.isDisabled
                            ? 'true'
                            : 'false',
                    )
                }

                this.$watch('error', syncAccessibilityAttributes)
                this.$watch('isLoading', syncAccessibilityAttributes)
            }

            this.initEditableColumn()

            this.$watch('state', (newState) => {
                if (
                    !isNative &&
                    this.select &&
                    this.select.state !== newState
                ) {
                    this.select.state = newState
                    this.select.updateSelectedDisplay()
                    this.select.renderOptions()
                }
            })
        },

        syncServerState() {
            editableColumn.syncServerState.call(this)

            if (
                !isNative &&
                this.select &&
                this.$refs.serverState?.hasAttribute('data-disabled')
            ) {
                if (this.$refs.serverState.dataset.disabled === 'true') {
                    this.select.disable()
                } else {
                    this.select.enable()
                }
            }
        },

        getServerState() {
            if (!this.$refs.serverState) {
                return undefined
            }

            return [null, undefined].includes(this.$refs.serverState.value)
                ? ''
                : this.$refs.serverState.value.replaceAll(
                      '\\' + String.fromCharCode(34),
                      String.fromCharCode(34),
                  )
        },

        destroy() {
            editableColumn.destroy.call(this)

            if (this.select) {
                this.select.destroy()
                this.select = null
            }
        },
    }
}
