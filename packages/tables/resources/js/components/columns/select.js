import { Select } from '../../../../../support/resources/js/utilities/select.js'

export default function selectTableColumn({
    canOptionLabelsWrap,
    canSelectPlaceholder,
    getOptionLabelUsing,
    getOptionsUsing,
    getSearchResultsUsing,
    hasDynamicOptions,
    hasDynamicSearchResults,
    initialOptionLabel,
    isDisabled,
    isHtmlAllowed,
    isNative,
    isSearchable,
    loadingMessage,
    name,
    noSearchResultsMessage,
    options,
    optionsLimit,
    placeholder,
    position,
    recordKey,
    searchableOptionFields,
    searchDebounce,
    searchingMessage,
    searchPrompt,
    state,
}) {
    return {
        error: undefined,

        isLoading: false,

        select: null,

        state,

        init() {
            if (!isNative) {
                this.select = new Select({
                    element: this.$refs.select,
                    options,
                    placeholder,
                    state: this.state,
                    canOptionLabelsWrap,
                    canSelectPlaceholder,
                    initialOptionLabel,
                    isHtmlAllowed,
                    isDisabled,
                    isSearchable,
                    getOptionLabelUsing,
                    getOptionsUsing,
                    getSearchResultsUsing,
                    hasDynamicOptions,
                    hasDynamicSearchResults,
                    searchPrompt,
                    searchDebounce,
                    loadingMessage,
                    searchingMessage,
                    noSearchResultsMessage,
                    optionsLimit,
                    position,
                    searchableOptionFields,
                    onStateChange: (newState) => {
                        this.state = newState
                    },
                })
            }

            Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail, respond }) => {
                    succeed(({ snapshot, effect }) => {
                        this.$nextTick(() => {
                            if (this.isLoading) {
                                return
                            }

                            if (
                                component.id !==
                                this.$root.closest('[wire\\:id]')?.attributes[
                                    'wire:id'
                                ].value
                            ) {
                                return
                            }

                            const serverState = this.getServerState()

                            if (
                                serverState === undefined ||
                                this.getNormalizedState() === serverState
                            ) {
                                return
                            }

                            this.state = serverState
                        })
                    })
                },
            )

            this.$watch('state', async (newState) => {
                if (
                    !isNative &&
                    this.select &&
                    this.select.state !== newState
                ) {
                    this.select.state = newState
                    this.select.updateSelectedDisplay()
                    this.select.renderOptions()
                }

                const serverState = this.getServerState()

                if (
                    serverState === undefined ||
                    this.getNormalizedState() === serverState
                ) {
                    return
                }

                this.isLoading = true

                const response = await this.$wire.updateTableColumnState(
                    name,
                    recordKey,
                    this.state,
                )

                this.error = response?.error ?? undefined

                if (!this.error && this.$refs.serverState) {
                    this.$refs.serverState.value = this.getNormalizedState()
                }

                this.isLoading = false
            })
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

        getNormalizedState() {
            const state = Alpine.raw(this.state)

            if ([null, undefined].includes(state)) {
                return ''
            }

            return state
        },
    }
}
