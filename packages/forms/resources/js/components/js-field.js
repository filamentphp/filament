import createJsRenderer, {
    snapshot,
} from '../../../../support/resources/js/js-renderer.js'

export default function jsFieldFormComponent({
    state,
    renderer,
    rendererConfiguration = {},
    fieldConfiguration,
}) {
    let lifecycle
    let destroyed = false
    let timer
    let pendingValue
    let callbacks
    const props = (state) => ({
        ...fieldConfiguration,
        value: snapshot(state),
        configuration: snapshot(rendererConfiguration),
        ...callbacks,
    })

    return {
        state,
        hasError: false,

        updateRendererConfiguration(value, inputConfiguration = {}) {
            if (destroyed) return
            rendererConfiguration = snapshot(value)
            fieldConfiguration = {
                ...fieldConfiguration,
                ...inputConfiguration,
            }
            if (
                fieldConfiguration.isDisabled ||
                fieldConfiguration.isReadOnly
            ) {
                clearTimeout(timer)
                pendingValue = undefined
            }
            this.updateRenderer()
        },

        reportError(phase, error) {
            console.error(
                `JS field ${phase} failed:`,
                {
                    statePath: this.$statePath,
                    renderer,
                },
                error,
            )
        },

        updateRenderer() {
            lifecycle?.update()
        },

        async init() {
            lifecycle = createJsRenderer({
                renderer,
                getBaseUrl: () => this.$el.ownerDocument.baseURI,
                getContext: () => this.getRendererContext(),
                getProps: () =>
                    this.state === undefined ? undefined : props(this.state),
                onError: (phase, error) => this.reportError(phase, error),
                onFailure: () => {
                    this.$refs.host.replaceChildren()
                    this.hasError = true
                },
                onDestroy: () => {
                    destroyed = true
                    clearTimeout(timer)
                },
            })
            await lifecycle.init()
        },

        getRendererContext() {
            const cancelPendingCommit = () => {
                clearTimeout(timer)
                pendingValue = undefined
            }
            const commit = () => {
                cancelPendingCommit()
                this.$wire.$commit()
            }

            this.$watch('state', (value) => {
                if (destroyed) {
                    return
                }

                if (JSON.stringify(value) !== pendingValue) {
                    cancelPendingCommit()
                }

                // A repeater path can disappear before Alpine destroys its host.
                if (value === undefined) {
                    return
                }

                this.updateRenderer()
            })

            const scope = this
            const utilities = {
                ...this.$schemaComponentMethods,
                // Preserve Livewire's proxy when frameworks deeply proxy `utilities`.
                get $wire() {
                    return scope.$wire
                },
                $callSchemaComponentMethod: this.$callSchemaComponentMethod,
                $get: this.$get,
                $set: this.$set,
                $statePath: this.$statePath,
                get $state() {
                    return scope.$state
                },
            }

            callbacks = {
                onChange: (value) => {
                    if (
                        destroyed ||
                        fieldConfiguration.isDisabled ||
                        fieldConfiguration.isReadOnly
                    ) {
                        return
                    }

                    const serialized = JSON.stringify(value)

                    if (serialized === JSON.stringify(this.state)) {
                        return
                    }

                    this.state = snapshot(value)
                    pendingValue = serialized
                    clearTimeout(timer)

                    if (
                        fieldConfiguration.isLive &&
                        !fieldConfiguration.isLiveOnBlur
                    ) {
                        timer = setTimeout(() => {
                            if (pendingValue === JSON.stringify(this.state)) {
                                commit()
                            }
                        }, fieldConfiguration.debounce ?? 0)
                    }

                    this.updateRenderer()
                },
                onBlur: () => {
                    if (
                        !destroyed &&
                        fieldConfiguration.isLive &&
                        fieldConfiguration.isLiveOnBlur &&
                        pendingValue !== undefined
                    ) {
                        commit()
                    }
                },
            }

            return {
                host: this.$refs.host,
                props: props(this.state),
                utilities,
            }
        },

        destroy() {
            lifecycle?.destroy()
        },
    }
}
