export default function selectTableColumn({ name, recordKey, state }) {
    return {
        error: undefined,

        isLoading: false,

        state,

        init: function () {
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
                                this.$root.closest('[wire\\:id]').attributes[
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

            this.$watch('state', async () => {
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

        getServerState: function () {
            if (!this.$refs.serverState) {
                return undefined
            }

            return [null, undefined].includes(this.$refs.serverState.value)
                ? ''
                : this.$refs.serverState.value
        },

        getNormalizedState: function () {
            const state = Alpine.raw(this.state)

            if ([null, undefined].includes(state)) {
                return ''
            }

            return state
        },
    }
}
