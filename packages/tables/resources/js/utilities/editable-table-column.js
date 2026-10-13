export default function editableTableColumn({
    name,
    recordKey,
    state,
    messages,
}) {
    const isMatchingSaveCall = (call) =>
        call.method === 'updateTableColumnState' &&
        call.params[0] === name &&
        String(call.params[1]) === String(recordKey)

    return {
        error: undefined,

        isLoading: false,

        isDestroyed: false,

        isRefreshing: false,

        hasRenderedState: false,

        state,

        status: undefined,

        unsubscribeLivewireHook: null,

        unsubscribeRequestHook: null,

        initEditableColumn() {
            this.unsubscribeLivewireHook = Livewire.interceptMessage(
                ({ message, onSuccess }) => {
                    if (message.component.id !== this.$wire.$id) {
                        return
                    }

                    const isSaveCommit = message.calls.some(isMatchingSaveCall)
                    const isRefreshCommit = this.isRefreshing

                    onSuccess(({ payload, onMorphed }) =>
                        onMorphed(() => {
                            const effects = payload.effects
                            if (isSaveCommit || isRefreshCommit) {
                                this.hasRenderedState = Boolean(effects.html)
                            }

                            if (isRefreshCommit) {
                                // Release ownership before Livewire sends another queued commit.
                                this.isRefreshing = false
                            }

                            if (!effects.html) {
                                return
                            }

                            this.$nextTick(() => {
                                if (!this.isDestroyed && !this.isLoading) {
                                    this.syncServerState()
                                }
                            })
                        }),
                    )
                },
            )

            this.$watch('state', async () => {
                const serverState = this.getServerState()

                if (
                    this.isDestroyed ||
                    this.isLoading ||
                    serverState === undefined ||
                    this.getNormalizedState() === serverState
                ) {
                    return
                }

                this.clearError()
                this.isLoading = true
                this.hasRenderedState = false
                this.status = messages?.saving

                this.unsubscribeRequestHook = Livewire.interceptRequest(
                    ({ request, onResponse }) => {
                        if (
                            !request.payload.components.some(
                                (component) =>
                                    JSON.parse(component.snapshot).memo.id ===
                                        this.$wire.$id &&
                                    (this.isRefreshing ||
                                        component.calls.some(
                                            isMatchingSaveCall,
                                        )),
                            )
                        ) {
                            return
                        }

                        onResponse(({ response }) => {
                            const readResponseText =
                                response.text.bind(response)

                            // Livewire 4.4.7 does not catch failures while reading non-streamed response bodies.
                            response.text = async () => {
                                try {
                                    return await readResponseText()
                                } catch (exception) {
                                    request.cancel()
                                    this.handleSaveFailure()

                                    throw exception
                                }
                            }
                        })
                    },
                )

                try {
                    const response = await this.$wire.updateTableColumnState(
                        name,
                        recordKey,
                        this.state,
                    )

                    await this.$nextTick()

                    // Batched renderless option requests can suppress the save's HTML.
                    if (!this.isDestroyed && !this.hasRenderedState) {
                        this.isRefreshing = true
                        await this.$wire.$refresh()
                        await this.$nextTick()

                        if (!this.hasRenderedState) {
                            this.handleSaveFailure()

                            return
                        }
                    }

                    this.unsubscribeRequestHook?.()
                    this.unsubscribeRequestHook = null

                    if (this.isDestroyed) {
                        return
                    }

                    this.error = response?.error ?? undefined
                    this.status = this.error ?? messages?.saved
                    this.isLoading = false

                    // The rendered value also supports `void` callbacks and model mutators.
                    this.syncServerState()
                } catch (exception) {
                    this.handleSaveFailure()
                }
            })
        },

        syncServerState() {
            const serverState = this.getServerState()

            if (serverState !== undefined) {
                this.state = serverState
            }
        },

        getNormalizedState() {
            return Alpine.raw(this.state) ?? ''
        },

        clearError() {
            if (!this.isLoading) {
                this.error = undefined
                this.status = undefined
            }
        },

        handleSaveFailure() {
            this.unsubscribeRequestHook?.()
            this.unsubscribeRequestHook = null

            if (this.isDestroyed) {
                return
            }

            // Published column views may not yet pass `messages` or render inline feedback.
            this.error =
                messages?.failure ??
                'The save could not be confirmed. Refresh the page before trying again.'
            this.status = this.error
            this.isRefreshing = false
            this.isLoading = false
        },

        blockEdit(event) {
            if (
                !this.isLoading ||
                (event.type === 'keydown' &&
                    ['Tab', 'Escape'].includes(event.key))
            ) {
                return
            }

            event.preventDefault()
            event.stopImmediatePropagation()
        },

        getAccessibilityAttributes(attributes = {}) {
            return {
                ...attributes,
                'aria-invalid': this.error
                    ? 'true'
                    : (attributes['aria-invalid'] ?? null),
                'aria-busy': this.isLoading
                    ? 'true'
                    : (attributes['aria-busy'] ?? null),
                'aria-describedby':
                    [
                        attributes['aria-describedby'],
                        this.error && messages
                            ? this.$id('table-column-error')
                            : null,
                    ]
                        .filter(Boolean)
                        .join(' ') || null,
            }
        },

        destroy() {
            this.isDestroyed = true
            this.unsubscribeLivewireHook?.()
            this.unsubscribeRequestHook?.()
            this.unsubscribeRequestHook = null
        },
    }
}
