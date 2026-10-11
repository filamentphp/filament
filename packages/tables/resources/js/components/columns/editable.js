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

        state,

        status: undefined,

        unsubscribeLivewireHook: null,

        unsubscribeRequestHook: null,

        initEditableColumn() {
            this.unsubscribeLivewireHook = Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail }) => {
                    if (component.id !== this.$wire.$id) {
                        return
                    }

                    if (commit.calls.some(isMatchingSaveCall)) {
                        // Livewire 3 does not settle method-call promises on failure.
                        fail(() => this.handleSaveFailure())
                    }

                    succeed(() => {
                        this.$nextTick(() => {
                            if (!this.isDestroyed && !this.isLoading) {
                                this.syncServerState()
                            }
                        })
                    })
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
                this.status = messages?.saving

                this.unsubscribeRequestHook = Livewire.hook(
                    'request',
                    ({ payload, respond }) => {
                        if (
                            !JSON.parse(payload).components.some(
                                (component) =>
                                    JSON.parse(component.snapshot).memo.id ===
                                        this.$wire.$id &&
                                    component.calls.some(isMatchingSaveCall),
                            )
                        ) {
                            return
                        }

                        respond(({ response }) => {
                            const readResponseText =
                                response.text.bind(response)

                            // Livewire 3's failure hook misses interrupted response bodies.
                            response.text = async () => {
                                try {
                                    return await readResponseText()
                                } catch (exception) {
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
